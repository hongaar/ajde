<?php

class Ajde_Crud_Cms_Meta_Type_Publish extends Ajde_Crud_Cms_Meta_Type
{
    public function beforeSave(MetaModel $meta, $value, Ajde_Model $model)
    {
        if ($value == 1) {
            // we need to publish this thing
            $stream = $meta->getOption('stream');
            $publisherClass = "Ajde_Publisher_" . ucfirst($stream);

            /* @var $publisher Ajde_Publisher */
            $publisher = new $publisherClass();

            if (strtolower($stream) == 'twitter') {
                $publisher->setOptions([
                    'consumerKey' => $meta->getOption('twitter_consumerkey'),
                    'consumerSecret' => $meta->getOption('twitter_consumersecret'),
                    'token' => $meta->getOption('twitter_token'),
                    'tokenSecret' => $meta->getOption('twitter_tokensecret')
                ]);
            }

            if (strtolower($stream) == 'mail') {
                $addresses = $model->getPublishRecipients();
                $publisher->setRecipients($addresses);
            }

            // fill with content
            $publishData = $model->getPublishData();

            $publisher->setTitle($publishData['title']);
            $publisher->setMessage($publishData['message']);
            $publisher->setImage($publishData['image']);
            $publisher->setUrl($publishData['url']);

            $value = $publisher->publish();
        }

        return $value;
    }

    public function getFields()
    {
        $this->help();
        $this->stream();
        $this->defaultToggle();
        $this->twitter();
        $this->facebook();
        $this->mail();

        return parent::getFields();
    }

    public function defaultToggle()
    {
        $field = $this->fieldFactory('default_toggle');
        $field->setType('boolean');
        $field->setLabel('Publish by default');
        $this->addField($field);
    }

    public function stream()
    {
        $field = $this->fieldFactory('stream');
        $field->setLabel('Publish to');
        $field->setType('enum');
        $field->setLength("Twitter,Facebook,Mail");
        $field->setIsRequired(true);
        $this->addField($field);
    }

    public function twitter()
    {
        $field = $this->fieldFactory('twitter_consumerkey');
        $field->setLabel('Twitter consumer key');
        $field->setType('text');
        $field->addShowOnlyWhen('stream', 'twitter');
        $this->addField($field);

        $field = $this->fieldFactory('twitter_consumersecret');
        $field->setLabel('Twitter consumer secret');
        $field->setType('text');
        $field->addShowOnlyWhen('stream', 'twitter');
        $this->addField($field);

        $field = $this->fieldFactory('twitter_token');
        $field->setLabel('Twitter token');
        $field->setType('text');
        $field->addShowOnlyWhen('stream', 'twitter');
        $this->addField($field);

        $field = $this->fieldFactory('twitter_tokensecret');
        $field->setLabel('Twitter token secret');
        $field->setType('text');
        $field->addShowOnlyWhen('stream', 'twitter');
        $this->addField($field);
    }

    public function facebook()
    {
    }

    public function mail()
    {
    }

    public function getMetaField(MetaModel $meta)
    {
        $field = $this->decorationFactory($meta);
        $field->setType('publish');
        if ($meta->getOption('default_toggle')) {
            $field->setDefault(1);
        }

        return $field;
    }
}
