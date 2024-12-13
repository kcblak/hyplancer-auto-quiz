# Hyplancer Auto Quiz

**Plugin Name:** Hyplancer Auto Quiz  
**Description:** Automatically generates a custom interactive quiz from text and replaces quiz text in posts, with GamiPress custom event for correct answers.  
**Version:** 1.2  
**Author:** Kingsley James-Hart

## Description

Hyplancer Auto Quiz is a WordPress plugin that parses quiz text in posts and converts it into an interactive quiz. The plugin supports GamiPress custom events to reward users for correct answers. 

## Features

- Automatically detects quiz text in post content.
- Converts text quizzes into interactive HTML quizzes.
- Integrates with GamiPress to trigger custom events for correct answers.
- Locks quizzes for 24 hours after an attempt.

## Installation

1. Download the plugin files.
2. Upload the `hyplancer-auto-quiz` folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage

1. Add quiz text in your post content in the following format:

Question: 
What is the capital of France?
 
Options: 
A. Berlin 
B. Madrid 
C. Paris 
D. Rome Correct

Option: C


2. The plugin will automatically replace the quiz text with an interactive quiz when the post is displayed.

## Custom GamiPress Event

This plugin adds a custom GamiPress event:

- **Quiz Answered Correctly:** Triggered when a user answers a quiz correctly.

## Development

### Functions

- `hyplancer_generate_and_replace_quiz($content)`: Replaces quiz text in post content with custom quiz HTML.
- `hyplancer_create_custom_quiz($question, $option_a, $option_b, $option_c, $option_d, $correct_answer)`: Generates custom quiz HTML and JavaScript.
- `hyplancer_save_quiz_attempt()`: Saves the user's quiz attempt timestamp and triggers GamiPress event if answered correctly.
- `hyplancer_custom_activity_triggers($triggers)`: Registers the custom GamiPress event.
- `hyplancer_custom_listener($user_id)`: Listens for the custom event and triggers GamiPress event.

### AJAX

- Registers AJAX actions to save quiz attempts using `wp_ajax_save_quiz_attempt` and `wp_ajax_nopriv_save_quiz_attempt`.

### Hooks

- `add_filter('the_content', 'hyplancer_generate_and_replace_quiz')`: Processes content to replace quiz text.
- `add_action('wp_ajax_save_quiz_attempt', 'hyplancer_save_quiz_attempt')`
- `add_action('wp_ajax_nopriv_save_quiz_attempt', 'hyplancer_save_quiz_attempt')`
- `add_filter('gamipress_activity_triggers', 'hyplancer_custom_activity_triggers')`
- `add_action('hyplancer_quiz_answered_correctly', 'hyplancer_custom_listener')`

## License

This plugin is licensed under the GPL v2.0 or later.
