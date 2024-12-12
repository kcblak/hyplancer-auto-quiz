<?php 
/*
Plugin Name: Hyplancer Auto Quiz
Description: Automatically generates a custom interactive quiz from text and replaces quiz text in posts, with GamiPress custom event for correct answers.
Version: 1.2
Author: James-Hart Kingsley
Author URI: https://www.linkedin.com/in/kingsley-james-hart-93679b184/?originalSubdomain=ng
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Hook into the save post process to replace quiz text with custom quiz HTML
function hyplancer_generate_and_replace_quiz($content) {
    // Match the quiz format in post content
    $pattern = '/Question:\s*(.*?)\s*Options:\s*A\.\s*(.*?)\s*B\.\s*(.*?)\s*C\.\s*(.*?)\s*D\.\s*(.*?)\s*Correct Option:\s*(.*?)\s*(?=\n|$)/s';

    // If there is a match, proceed to create a custom HTML quiz and replace the content
    if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $question = $match[1];
            $option_a = $match[2];
            $option_b = $match[3];
            $option_c = $match[4];
            $option_d = $match[5];
            $correct_answer = $match[6];

            // Generate the custom quiz HTML
            $quiz_html = hyplancer_create_custom_quiz($question, $option_a, $option_b, $option_c, $option_d, $correct_answer);

            // Replace the quiz text with the generated quiz HTML
            $content = str_replace($match[0], $quiz_html, $content);
        }
    }

    return $content;
}

// Add the filter to process content before it is saved
add_filter('the_content', 'hyplancer_generate_and_replace_quiz');

// Helper function to generate the custom quiz HTML and JS
function hyplancer_create_custom_quiz($question, $option_a, $option_b, $option_c, $option_d, $correct_answer) {
    $user_id = get_current_user_id();
    $post_id = get_the_ID();

    // Get the user's last attempt timestamp for this post
    $last_attempt_time = get_user_meta($user_id, 'hyplancer_quiz_' . $post_id, true);

    // Check if the user has taken the quiz in the last 24 hours
    $current_time = current_time('timestamp');
    $quiz_locked = false;
    
    if ($last_attempt_time && ($current_time - $last_attempt_time) < 86400) { // 86400 seconds = 24 hours
        $quiz_locked = true;
    }

    // Quiz HTML structure with updated styles
    $quiz_html = '
    <div class="hyplancer-quiz">
        <p><strong>Question:</strong> ' . esc_html($question) . '</p>
        <form class="quiz-form">
            <label><input type="radio" name="answer" value="A"> ' . esc_html($option_a) . '</label><br>
            <label><input type="radio" name="answer" value="B"> ' . esc_html($option_b) . '</label><br>
            <label><input type="radio" name="answer" value="C"> ' . esc_html($option_c) . '</label><br>
            <label><input type="radio" name="answer" value="D"> ' . esc_html($option_d) . '</label><br>
            <button type="button" class="submit-quiz" ' . ($quiz_locked ? 'disabled' : '') . '>Submit</button>
        </form>
        <div class="quiz-feedback"></div>
    </div>
    <script>
    document.querySelector(".submit-quiz").addEventListener("click", function() {
        var selectedAnswer = document.querySelector("input[name=\'answer\']:checked");
        var feedback = document.querySelector(".quiz-feedback");

        if (selectedAnswer) {
            var answer = selectedAnswer.value;
            var correctAnswer = "' . esc_js($correct_answer) . '";
            
            if (answer === correctAnswer) {
                feedback.innerHTML = "<p>Correct!</p>";
                // Trigger the custom GamiPress event when the user answers correctly
                var data = {
                    action: "save_quiz_attempt",
                    user_id: "' . esc_js($user_id) . '",
                    post_id: "' . esc_js($post_id) . '",
                    timestamp: "' . esc_js($current_time) . '",
                    correct: true
                };
                jQuery.post(ajaxurl, data, function() {
                    location.reload();
                });
            } else {
                feedback.innerHTML = "<p>Incorrect. The correct answer is " + correctAnswer + ".</p>";
                // Lock the quiz for this user by saving the attempt timestamp
                var data = {
                    action: "save_quiz_attempt",
                    user_id: "' . esc_js($user_id) . '",
                    post_id: "' . esc_js($post_id) . '",
                    timestamp: "' . esc_js($current_time) . '"
                };
                jQuery.post(ajaxurl, data, function() {
                    location.reload();
                });
            }
        } else {
            feedback.innerHTML = "<p>Please select an answer.</p>";
        }
    });
    </script>
    <style>
    .hyplancer-quiz { 
        border: 1px solid #ccc; 
        padding: 15px; 
        background-color: black; 
        color: green; 
        font-family: Arial, sans-serif;
    }
    .quiz-form { 
        margin-bottom: 15px; 
    }
    .quiz-feedback { 
        font-weight: bold; 
        margin-top: 10px; 
        color: green;
    }
    .quiz-form label { 
        color: green; 
    }
    .submit-quiz { 
        background-color: #333; 
        color: green; 
        padding: 10px 20px; 
        border: none; 
        cursor: pointer; 
        font-size: 16px; 
    }
    .submit-quiz:hover {
        background-color: #555; 
    }
    </style>
    ';

    return $quiz_html;
}

// Save the user's attempt timestamp
function hyplancer_save_quiz_attempt() {
    $user_id = $_POST['user_id'];
    $post_id = $_POST['post_id'];
    $timestamp = $_POST['timestamp'];
    $correct = isset($_POST['correct']) ? $_POST['correct'] : false;

    update_user_meta($user_id, 'hyplancer_quiz_' . $post_id, $timestamp);

    // If the quiz was answered correctly, trigger the GamiPress custom event
    if ($correct) {
        do_action('hyplancer_quiz_answered_correctly', $user_id);
    }

    wp_die(); // Required to terminate the AJAX request
}

// Register AJAX action to save the quiz attempt timestamp
add_action('wp_ajax_save_quiz_attempt', 'hyplancer_save_quiz_attempt');
add_action('wp_ajax_nopriv_save_quiz_attempt', 'hyplancer_save_quiz_attempt');

// Register the custom event in GamiPress
function hyplancer_custom_activity_triggers($triggers) {
    $triggers['Hyplancer Events'] = array(
        'hyplancer_quiz_answered_correctly' => __('Quiz Answered Correctly', 'gamipress'),
    );
    return $triggers;
}
add_filter('gamipress_activity_triggers', 'hyplancer_custom_activity_triggers');

// Listener for the custom event
function hyplancer_custom_listener($user_id) {
    // Trigger the custom GamiPress event
    gamipress_trigger_event(array(
        'event' => 'hyplancer_quiz_answered_correctly',
        'user_id' => $user_id
    ));
}

// Hook into the custom action when the user answers the quiz correctly
add_action('hyplancer_quiz_answered_correctly', 'hyplancer_custom_listener');
