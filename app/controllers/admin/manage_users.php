<?php
/**
 * Backward compatibility wrapper for old manage_users.php
 * All functionality moved to AdminUserController.php
 */
require_once __DIR__ . '/AdminUserController.php';

// Re-export class as UserController for backward compatibility
class UserController extends AdminUserController {
    // All methods inherited from AdminUserController
}
?>
