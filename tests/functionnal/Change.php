<?php
/**
 * ---------------------------------------------------------------------
 * GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2015-2022 Teclib' and contributors.
 *
 * http://glpi-project.org
 *
 * based on GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2003-2014 by the INDEPNET Development Team.
 *
 * ---------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of GLPI.
 *
 * GLPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GLPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GLPI. If not, see <http://www.gnu.org/licenses/>.
 * ---------------------------------------------------------------------
 */

namespace tests\units;

use DbTestCase;

/* Test for inc/change.class.php */

class Change extends DbTestCase {

   public function testAddFromItem() {
      // add change from a computer
      $computer   = getItemByTypeName('Computer', '_test_pc01');
      $change     = new \Change;
      $changes_id = $change->add([
         'name'           => "test add from computer \'_test_pc01\'",
         'content'        => "test add from computer \'_test_pc01\'",
         '_add_from_item' => true,
         '_from_itemtype' => 'Computer',
         '_from_items_id' => $computer->getID(),
      ]);
      $this->integer($changes_id)->isGreaterThan(0);
      $this->boolean($change->getFromDB($changes_id))->isTrue();

      // check relation
      $change_item = new \Change_Item;
      $this->boolean($change_item->getFromDBForItems($change, $computer))->isTrue();
   }

   public function testAddAdditionalActorsDuplicated() {
      $this->login();
      $change = new \Change;
      $changes_id = $change->add([
         'name'           => "test add additional actors duplicated",
         'content'        => "test add additional actors duplicated",
      ]);
      $this->integer($changes_id)->isGreaterThan(0);

      $users_id = getItemByTypeName('User', TU_USER, true);

      $result = $change->update([
         'id'                       => $changes_id,
         '_additional_requesters'   => [
            [
               'users_id' => $users_id,
               'use_notification'  => 0,
            ]
         ]
      ]);
      $this->boolean($result)->isTrue();

      $result = $change->update([
         'id'                       => $changes_id,
         '_additional_requesters'   => [
            [
               'users_id' => $users_id,
               'use_notification'  => 0,
            ]
         ]
      ]);
      $this->boolean($result)->isTrue();
   }
}
