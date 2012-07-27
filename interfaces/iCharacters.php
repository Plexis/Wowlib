<?php
namespace Wowlib;

/*
| ---------------------------------------------------------------
| Characters Interface
| ---------------------------------------------------------------
|
*/
interface iCharacters
{
    public function nameExists($name);
    public function fetch($id);
    public function getOnlineCount($faction = 0);
    public function getOnlineList($faction = 0, $limit = 50, $offset = 0, $where = null);
    public function listCharacters($acct = 0, $limit = 50, $start = 0);
    public function topKills($faction, $limit, $start);
    public function delete($id);
    public function loginFlags();
    public function flagToBit($flag);
    public function raceToText($id);
    public function classToText($id);
    public function genderToText($id);
}