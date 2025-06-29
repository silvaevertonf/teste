?php

include ("../../inc/includes.php");
Session::checkRight("config", READ);

if (!defined("GLPI_MOD_DIR")) {
   define("GLPI_MOD_DIR", GLPI_ROOT."/plugins/livechat");
}

$plugin = new Plugin();

/**
 * Desativar LiveChat
 */
function lvOff() {
    $source = GLPI_MOD_DIR . "/livechat.js";
    $destination = GLPI_MOD_DIR . "/example.js";

    if (file_exists($source)) {
        if (!rename($source, $destination)) {
            die(__('Erro ao desativar o LiveChat.', 'livechat'));
        }
    }
    Html::redirect($CFG_GLPI["root_doc"] . "/front/plugin.php");
}

/**
 * Ativar LiveChat
 */
function lvOn() {
    $source = GLPI_MOD_DIR . "/example.js";
    $destination = GLPI_MOD_DIR . "/livechat.js";

    if (file_exists($source)) {
        if (!rename($source, $destination)) {
            die(__('Erro ao ativar o LiveChat.', 'livechat'));
        }
    }
    Html::redirect($CFG_GLPI["root_doc"] . "/front/plugin.php");
}

/**
 * Atualizar URL do LiveChat
 */
function urlW($server, $port) {
    $filepath = GLPI_MOD_DIR . "/livechat.js";
    if (file_exists($filepath)) {
        $script_livechat = explode("\n", file_get_contents($filepath));
        $script_livechat[3] = "j.async = true; j.src = 'http://".$server.":".$port."/livechat/rocketchat-livechat.min.js?_=201903270000';";
        $script_livechat[5] = "})(window, document, 'script', 'http://".$server.":".$port."/livechat');";

        if (file_put_contents($filepath, implode("\n", $script_livechat)) === false) {
            die(__('Não foi possível atualizar o script.', 'livechat'));
        }

        echo __('Script atualizado com sucesso.', 'livechat');
    } else {
        die(__('Arquivo livechat.js não encontrado.', 'livechat'));
    }
}

// Checar ativação do plugin
if ($plugin->isActivated("livechat")) {
    $action = $_REQUEST['act'] ?? '';

    Html::header(__('Plugin LiveChat', 'livechat'), "", "plugins", "livechat");

    echo "<div class='center' style='height:1100px; width:80%; background:#fff; margin:auto; float:none;'><br><p>";
    echo "<div id='config' class='center here ui-tabs-panel'>";
    echo "<br><p><span style='color:blue; font-weight:bold; font-size:13pt;'>".__('Plugin LiveChat', 'livechat')."</span><br><br><p>";

    echo "<div style='text-align: left;'><p><strong>".__('Para adicionar seu LiveChat no GLPI, siga as instruções abaixo:', 'livechat')."</strong></p>
    <p>1. ".__('Acesse o diretório', 'livechat')." <code>livechat</code>, ".__('dentro de', 'livechat')." <code>plugins</code>.<br>
    2. ".__('Abra o arquivo', 'livechat')." <code>example.js</code>.<br>
    3. ".__('Altere os dados de IP e porta, conforme o seu servidor Rocket.Chat.', 'livechat')."<br>
    4. ".__('Se estiver usando outro serviço de LiveChat, substitua o conteúdo pelo script do servidor.', 'livechat')."<br>
    5. ".__('Por fim, clique no botão Habilitar.', 'livechat')."</p></div>";

    // Botões de ativar/desativar
    echo "<table style='text-align: left;' class='tab_cadrehov' border='0'><tbody>";
    echo "<tr><td>".__('LiveChat:', 'livechat')."</td>";

    if (file_exists(GLPI_MOD_DIR . "/example.js")) {
        echo "<td>
              <form action='config.php?act=lvdon' method='post'>";
        if ($action === 'lvdon') {
            lvOn();
        }
        echo "<input class='submit' type='submit' value='"._x('button','Enable', 'livechat')."' />";
        Html::closeForm();
        echo "</td>";
    } elseif (file_exists(GLPI_MOD_DIR . "/livechat.js")) {
        echo "<td>
              <form action='config.php?act=lvoff' method='post'>";
        if ($action === 'lvoff') {
            lvOff();
        }
        echo "<input class='submit' type='submit' value='"._x('button','Disable', 'livechat')."' />";
        Html::closeForm();
        echo "</td>";
    }
    echo "</tr></tbody></table>";

    Html::footer();
}
