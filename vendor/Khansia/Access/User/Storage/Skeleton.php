<?php


namespace Khansia\Access\User\Storage;

interface Skeleton {

  public function load($id, $mode = \Khansia\Access\User\Storage::LOADBY_ID);
  //public function loadList($limit = 0);
  public function save(\Khansia\Access\User $user, $update = false);
  public function checkDuplicate($id, $email);
}