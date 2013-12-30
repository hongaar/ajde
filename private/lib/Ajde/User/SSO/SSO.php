<?php

interface Ajde_User_SSO
{
    public function getAuthenticationURL($returnto = '');

    public function getAccessToken();

    /**
     * @return Ajde_User
     */
    public function getUser();

    public function getUsernameSuggestion();

    public function getToken();

    public function getData();
}