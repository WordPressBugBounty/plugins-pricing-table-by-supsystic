<?php
class admin_navControllerPts extends controllerPts
{
  public function getPermissions()
  {
    return [
      PTS_USERLEVELS => [
        PTS_ADMIN => [],
      ],
    ];
  }
}
