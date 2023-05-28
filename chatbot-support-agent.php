<?php
/*
Plugin Name: Chatbot Support Agent
Plugin URI: http://yourwebsite.com
Description: A WordPress plugin for integrating a chatbot support agent using the ChatGPT API.
Version: 1.0.0
Author: Your Name
Author URI: http://yourwebsite.com
License: GPL2
*/

// Register the plugin settings
function chatbot_support_agent_register_settings() {
    add_option('chatbot-support-agent-api-key', '');
    add_option('chatbot-support-agent-instructions', '');

    register_setting('chatbot-support-agent-settings-group', 'chatbot-support-agent-api-key');
    register_setting('chatbot-support-agent-settings-group', 'chatbot-support-agent-instructions');
}
add_action('admin_init', 'chatbot_support_agent_register_settings');

// Add the plugin settings page
function chatbot_support_agent_add_settings_page() {
    add_options_page('Chatbot Support Agent Settings', 'Chatbot Support Agent', 'manage_options', 'chatbot-support-agent', 'chatbot_support_agent_render_settings_page');
}
add_action('admin_menu', 'chatbot_support_agent_add_settings_page');

// Render the plugin settings page
function chatbot_support_agent_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Chatbot Support Agent Settings</h1>

        <form method="post" action="options.php">
            <?php settings_fields('chatbot-support-agent-settings-group'); ?>
            <?php do_settings_sections('chatbot-support-agent-settings-group'); ?>
            
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">API Key:</th>
                    <td><input type="text" name="chatbot-support-agent-api-key" value="<?php echo esc_attr(get_option('chatbot-support-agent-api-key')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Instructions:</th>
                    <td><textarea name="chatbot-support-agent-instructions"><?php echo esc_textarea(get_option('chatbot-support-agent-instructions')); ?></textarea></td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Enqueue necessary scripts and styles
function chatbot_support_agent_enqueue_scripts() {
    wp_enqueue_script('chatbot-support-agent-script', plugins_url('js/chatbot-support-agent.js', __FILE__), array('jquery'), '1.0', true);

    // Pass API key, instructions, and avatar image URL to the JavaScript file
    $api_key = get_option('chatbot-support-agent-api-key');
    $instructions = get_option('chatbot-support-agent-instructions');
    $avatar_url = plugins_url('images/avatar.png', __FILE__); // Replace 'images/avatar.png' with the actual path to your avatar image

    $script_data = array(
        'api_key' => $api_key,
        'instructions' => $instructions,
        'avatar_url' => $avatar_url
    );

    wp_localize_script('chatbot-support-agent-script', 'chatbotSupportAgentData', $script_data);

    wp_enqueue_style('chatbot-support-agent-style', plugins_url('css/chatbot-support-agent.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'chatbot_support_agent_enqueue_scripts');

// Add the chatbot interface to the WordPress website
function chatbot_support_agent_add_interface() {
    echo '<div id="chatbot-support-agent"></div>';
}
add_action('wp_footer', 'chatbot_support_agent_add_interface');
?>

<script>
    jQuery(document).ready(function($) {
        // Retrieve API key and instructions from the localized script data
        var api_key = chatbotSupportAgentData.api_key;
        var instructions = chatbotSupportAgentData.instructions;
        var avatar_url = chatbotSupportAgentData.avatar_url;

        // Chatbot support agent initialization
        function initChatbot() {
            var chatContainer = '<div class="chat-container">' +
                                    '<div class="chat-header">' +
                                        '<img class="avatar" src="' + avatar_url + '" alt="Avatar">' +
                                        '<h3 class="agent-name">Support Agent</h3>' +
                                    '</div>' +
                                    '<div class="chat-messages"></div>' +
                                    '<div class="chat-input">' +
                                        '<input type="text" id="chat-input-box" placeholder="Type your message here">' +
                                        '<button id="chat-send-btn">Send</button>' +
                                    '</div>' +
                                '</div>';

            $('#chatbot-support-agent').html(chatContainer);
        }

        // Send message to the chatbot
        function sendMessage(message) {
            $.ajax({
                url: 'your_api_endpoint',
                type: 'POST',
                data: {
                    message: message,
                    api_key: api_key,
                    instructions: instructions
                },
                success: function(response) {
                    // Handle the API response and display the chatbot's message
                    var chatbotMessage = '<div class="chat-message">' + response + '</div>';
                    $('.chat-messages').append(chatbotMessage);
                    $('.chat-container').scrollTop($('.chat-container')[0].scrollHeight);
                }
            });
        }

        // Handle send button click
        $('#chat-send-btn').click(function() {
            var message = $('#chat-input-box').val().trim();
            if (message !== '') {
                // Append user message to the chat container
                var userMessage = '<div class="chat-message user">' + message + '</div>';
                $('.chat-messages').append(userMessage);

                // Send the message to the server
                sendMessage(message);
            }
        });

        // Handle enter key press in the input box
        $('#chat-input-box').keypress(function(event) {
            if (event.which === 13) {
                event.preventDefault();
                var message = $('#chat-input-box').val().trim();
                if (message !== '') {
                    // Append user message to the chat container
                    var userMessage = '<div class="chat-message user">' + message + '</div>';
                    $('.chat-messages').append(userMessage);

                    // Send the message to the server
                    sendMessage(message);
                }
            }
        });

        // Initialize the chatbot support agent
        initChatbot();
    });
</script>
