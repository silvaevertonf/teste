<?php
/*
 -------------------------------------------------------------------------
 livechat plugin for GLPI
 Copyright (C) 2019 by the livechat Development Team.

 https://github.com/pluginsGLPI/livechat
 -------------------------------------------------------------------------

 LICENSE

 This file is part of livechat.

 livechat is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 livechat is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with livechat. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

 /**
 /**
 * Plugin install process
 *
 * @return boolean
 */
function plugin_livechat_install() {
   global $DB;

   // Instanciar a migração com a versão do plugin
   $migration = new Migration(104); // Versão do plugin (ex.: 1.0.4)

   // Criar tabela apenas se ainda não existir
   if (!$DB->tableExists('glpi_plugin_livechat_configs')) {
      $query = "CREATE TABLE `glpi_plugin_livechat_configs` (
                  `id` INT(11) NOT NULL AUTO_INCREMENT,
                  `name` VARCHAR(255) NOT NULL,
                  PRIMARY KEY  (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
      $DB->queryOrDie($query, $DB->error());
   }

   // Adicionar campo e índice à tabela se já existir
   if ($DB->tableExists('glpi_plugin_livechat_configs')) {
      if (!$migration->fieldExists('glpi_plugin_livechat_configs', 'value')) {
         $migration->addField(
            'glpi_plugin_livechat_configs',
            'value',
            'string'
         );
      }

      if (!$migration->keyExists('glpi_plugin_livechat_configs', 'name')) {
         $migration->addKey(
            'glpi_plugin_livechat_configs',
            'name'
         );
      }
   }

   // Executar toda a migração
   $migration->executeMigration();

   return true;
}

/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_livechat_uninstall() {
   global $DB;

   $tables = ['configs'];

   foreach ($tables as $table) {
      $tablename = 'glpi_plugin_livechat_' . $table;

      // Remover a tabela se ela existir
      if ($DB->tableExists($tablename)) {
         if (!$DB->query("DROP TABLE `$tablename`")) {
            return false; // Abortar caso a exclusão falhe
         }
      }
   }

   return true;
}