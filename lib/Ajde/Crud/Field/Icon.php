<?php

class Ajde_Crud_Field_Icon extends Ajde_Crud_Field
{
    private $defaultPackage = 'bootstrap-2-default';

    public function getIconPackage()
    {
        if (parent::hasIconPackage()) {
            return $this->iconPackage(parent::getIconPackage());
        } else {
            return $this->iconPackage($this->defaultPackage);
        }
    }

    private function iconPackage($package)
    {
        switch ($package) {
            case "bootstrap-2-default":
                return $this->bootstrap2();
                break;
            case "bootstrap-2-nodetypes":
                return $this->bootstrap2Nodetypes();
                break;
        }
    }

    protected function _getHtmlAttributes()
    {
        $attributes          = [];
        $attributes['type']  = "hidden";
        $attributes['value'] = $this->getValue();

        return $attributes;
    }

    private function bootstrap2()
    {
        return $this->convertBootstrapIcons([
            "icon-glass",
            "icon-music",
            "icon-search",
            "icon-envelope",
            "icon-heart",
            "icon-star",
            "icon-star-empty",
            "icon-user",
            "icon-film",
            "icon-th-large",
            "icon-th",
            "icon-th-list",
            "icon-ok",
            "icon-remove",
            "icon-zoom-in",
            "icon-zoom-out",
            "icon-off",
            "icon-signal",
            "icon-cog",
            "icon-trash",
            "icon-home",
            "icon-file",
            "icon-time",
            "icon-road",
            "icon-download-alt",
            "icon-download",
            "icon-upload",
            "icon-inbox",
            "icon-play-circle",
            "icon-repeat",
            "icon-refresh",
            "icon-list-alt",
            "icon-lock",
            "icon-flag",
            "icon-headphones",
            "icon-volume-off",
            "icon-volume-down",
            "icon-volume-up",
            "icon-qrcode",
            "icon-barcode",
            "icon-tag",
            "icon-tags",
            "icon-book",
            "icon-bookmark",
            "icon-print",
            "icon-camera",
            "icon-font",
            "icon-bold",
            "icon-italic",
            "icon-text-height",
            "icon-text-width",
            "icon-align-left",
            "icon-align-center",
            "icon-align-right",
            "icon-align-justify",
            "icon-list",
            "icon-indent-left",
            "icon-indent-right",
            "icon-facetime-video",
            "icon-picture",
            "icon-pencil",
            "icon-map-marker",
            "icon-adjust",
            "icon-tint",
            "icon-edit",
            "icon-share",
            "icon-check",
            "icon-move",
            "icon-step-backward",
            "icon-fast-backward",
            "icon-backward",
            "icon-play",
            "icon-pause",
            "icon-stop",
            "icon-forward",
            "icon-fast-forward",
            "icon-step-forward",
            "icon-eject",
            "icon-chevron-left",
            "icon-chevron-right",
            "icon-plus-sign",
            "icon-minus-sign",
            "icon-remove-sign",
            "icon-ok-sign",
            "icon-question-sign",
            "icon-info-sign",
            "icon-screenshot",
            "icon-remove-circle",
            "icon-ok-circle",
            "icon-ban-circle",
            "icon-arrow-left",
            "icon-arrow-right",
            "icon-arrow-up",
            "icon-arrow-down",
            "icon-share-alt",
            "icon-resize-full",
            "icon-resize-small",
            "icon-plus",
            "icon-minus",
            "icon-asterisk",
            "icon-exclamation-sign",
            "icon-gift",
            "icon-leaf",
            "icon-fire",
            "icon-eye-open",
            "icon-eye-close",
            "icon-warning-sign",
            "icon-plane",
            "icon-calendar",
            "icon-random",
            "icon-comment",
            "icon-magnet",
            "icon-chevron-up",
            "icon-chevron-down",
            "icon-retweet",
            "icon-shopping-cart",
            "icon-folder-close",
            "icon-folder-open",
            "icon-resize-vertical",
            "icon-resize-horizontal",
            "icon-hdd",
            "icon-bullhorn",
            "icon-bell",
            "icon-certificate",
            "icon-thumbs-up",
            "icon-thumbs-down",
            "icon-hand-right",
            "icon-hand-left",
            "icon-hand-up",
            "icon-hand-down",
            "icon-circle-arrow-right",
            "icon-circle-arrow-left",
            "icon-circle-arrow-up",
            "icon-circle-arrow-down",
            "icon-globe",
            "icon-wrench",
            "icon-tasks",
            "icon-filter",
            "icon-briefcase",
            "icon-fullscreen"
        ]);
    }

    private function bootstrap2Nodetypes()
    {
        return $this->bootstrap2();
    }

    private function convertBootstrapIcons($icons)
    {
        return $icons;
    }
}
