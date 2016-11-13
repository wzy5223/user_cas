<?php
/**
 * ownCloud - user_cas
 *
 * @author Felix Rupp <kontakt@felixrupp.com>
 * @copyright Felix Rupp <kontakt@felixrupp.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\User_CAS\AppInfo;

use \OCP\AppFramework\App;
use \OCP\IContainer;

use \OCA\User_CAS\Service\UserService;
use \OCA\User_CAS\Hooks\UserHooks;

require_once __DIR__ . '/../vendor/phpCAS/CAS.php';

/**
 * Class Application
 *
 * @package OCA\User_CAS\AppInfo
 *
 * @author Felix Rupp <kontakt@felixrupp.com>
 * @copyright Felix Rupp <kontakt@felixrupp.com>
 *
 * @since 1.4.0
 */
class Application extends App
{

    /**
     * Application constructor.
     *
     * @param array $urlParams
     */
    public function __construct(array $urlParams = array())
    {

        parent::__construct('user_cas', $urlParams);

        $container = $this->getContainer();

        /**
         * Register UserService with UserSession for login/logout and UserManager for create
         */
        $container->registerService('UserService', function (IContainer $c) {
            return new UserService(
                $c->query('ServerContainer')->getUserManager(), $c->query('ServerContainer')->getUserSession()
            );
        });

        /**
         * Register UserHooks
         */
        $container->registerService('UserHooks', function (IContainer $c) {
            return new UserHooks(
                $c->query('ServerContainer')->getUserManager(), $c->query('ServerContainer')->getUserSession(), $c->query('UserService')
            );
        });

        // currently logged in user, userId can be gotten by calling the
        // getUID() method on it
        $container->registerService('User', function (IContainer $c) {
            return $c->query('UserSession')->getUser();
        });
    }
}