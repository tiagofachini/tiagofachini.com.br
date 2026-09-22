<?php


/**
 * User can or has permission type
 *
 * @param string $permissionType
 * @param int|null $userID
 *
 * @return bool
 */
function cs_permission_user_can($permissionType, $userID = null) {
  return cornerstone('Permissions')->userCan($permissionType, $userID);
}


/**
 * Check if current user can edit anything in Cornerstone
 *
 * @return bool
 */
function cs_permission_user_can_edit_anything() {
  return cornerstone('Permissions')->userCanEditAnything();
}

function cs_permission_user_can_edit_anything_assert() {
  if (cs_permission_user_can_edit_anything()) {
    return;
  }

  throw new DomainException(__('User does not have permission to edit anything in Cornerstone', 'cornerstone'));
}
