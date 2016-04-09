<?php

class Ajde_Component_Image extends Ajde_Component
{
    public static function processStatic(Ajde_Template_Parser $parser, $attributes)
    {
        $instance = new self($parser, $attributes);

        return $instance->process();
    }

    protected function _init()
    {
        return [
            'base64'   => 'base64',
            'filename' => 'html',
            'output'   => 'image'
        ];
    }

    public function process()
    {
        switch ($this->_attributeParse()) {
            case 'base64':
                $image = new Ajde_Resource_Image($this->attributes['filename']);
                $image->setWidth(issetor($this->attributes['width']));
                $image->setHeight(issetor($this->attributes['height']));
                $image->setCrop(Ajde_Component_String::toBoolean(issetor($this->attributes['crop'], true)));

                $controller = Ajde_Controller::fromRoute(new Ajde_Core_Route('_core/component:imageBase64'));
                $controller->setImage($image);
                $controller->setWidth(issetor($this->attributes['width'], null));
                $controller->setHeight(issetor($this->attributes['height'], null));
                $controller->setExtraClass(issetor($this->attributes['class'], ''));

                return $controller->invoke();
                break;
            case 'html':
                return self::getImageTag(
                    $this->attributes['filename'],
                    issetor($this->attributes['width']),
                    issetor($this->attributes['height']),
                    Ajde_Component_String::toBoolean(issetor($this->attributes['crop'], true)),
                    issetor($this->attributes['class'], ''),
                    issetor($this->attributes['lazy'], false),
                    issetor($this->attributes['absoluteUrl'], false)
                );
                break;
            case 'image':
                return false;
                break;
        }
        // TODO:
        throw new Ajde_Component_Exception('Missing required attributes for component call');
    }

    public static function getImageTag(
        $filename,
        $width = null,
        $height = null,
        $crop = true,
        $class = '',
        $lazy = false,
        $absoluteUrl = false
    ) {
        $image = new Ajde_Resource_Image($filename);
        $image->setWidth($width);
        $image->setHeight($height);
        $image->setCrop($crop);

        $controller = Ajde_Controller::fromRoute(new Ajde_Core_Route('_core/component:image'));
        $controller->setImage($image);
        $controller->setExtraClass($class);
        $controller->setLazy($lazy);
        $controller->setAbsoluteUrl($absoluteUrl);

        return $controller->invoke();
    }
}
