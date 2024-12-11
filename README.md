Hyplancer Auto Quiz WordPress Plugin Documentation
Overview
Plugin Name: Hyplancer Auto Quiz
Description: Automatically generates a custom interactive quiz from text and replaces quiz text in posts, with GamiPress custom event for correct answers.
Version: 1.2
Author: Kingsley James-Hart

Features
Automatically identifies quiz questions in post content.
Generates interactive quiz HTML and JavaScript.
Prevents users from taking the same quiz within 24 hours.
Integrates with GamiPress to trigger a custom event when a user answers a quiz correctly.
Customizable quiz appearance with CSS.
Installation
Download the Plugin:

Clone or download the plugin files from the GitHub repository (steps to create and upload to GitHub are detailed below).
Upload the Plugin:

Log in to your WordPress admin dashboard.
Go to Plugins > Add New > Upload Plugin.
Upload the hyplancer-auto-quiz.zip file.
Click "Install Now" and then "Activate".
Verify GamiPress Installation:

Ensure that the GamiPress plugin is installed and activated on your WordPress site.
Usage
Create a Quiz in Your Post:

Format your quiz questions in the post content using the following structure:
mathematica
Copy code
Question: What is the capital of France?
Options:
A. Berlin
B. Madrid
C. Paris
D. Rome
Correct Option: C
Publish or Update the Post:

When the post is published or updated, the plugin will automatically replace the quiz text with interactive quiz HTML.
User Interaction:

Users can attempt the quiz. If they answer correctly, a custom GamiPress event will be triggered.
Users will be locked from taking the quiz again for 24 hours.
Custom Events Integration with GamiPress
The plugin integrates with GamiPress to trigger a custom event when a quiz is answered correctly. This allows you to reward users with points, achievements, or ranks based on their quiz performance.

