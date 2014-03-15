<?php

class LogModel extends Ajde_Model
{
    public function displayPanel()
    {
        $controller = Ajde_Controller::fromRoute(new Ajde_Core_Route('admin/system:logpanel'));
        $controller->setItem($this);
        return $controller->invoke();
    }

    public function displayLevel()
    {
        $severity = explode(':', $this->getLevel());

        return "<span class='label label-" . $this->getLevelColor($severity[0]) . "'>" . $severity[1] . '</span>';
    }

    public function displayChannel()
    {
        $channel = $this->getChannel();
        return '<i class="' . $this->getChannelIcon($channel) . '"></i>' . $channel;

    }

    public function getLevelColor($level)
    {
        switch ($level) {
            case 1:
            case 2:
            case 3:
            case 4:
                return 'important';
            case 5:
                return 'warning';
            case 6:
                return '';
            case 7:
            case 8:
            default:
                return 'info';
        }
    }

    public function getChannelIcon($channel)
    {
        switch ($channel) {
            case 'Exception':
                return 'icon-exclamation-sign';
            case 'Error':
                return 'icon-exclamation-sign';
            case 'Routing':
                return 'icon-repeat';
            case 'Security':
                return 'icon-eye-open';
            case 'Info':
                return 'icon-info-sign';
            case 'Application':
                return 'icon-globe';
        }
    }
}
