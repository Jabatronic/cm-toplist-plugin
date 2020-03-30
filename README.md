# Catena Media Technical Challenge #

# IMPORTANT!

I have NOT YET solved some rest API authentication issues with this plugin. As such this plugin is vulnerable to security threats!

It must not be used in a production environment!! I recommend testing in a VM or Container.

This was a deep dive for me. My experience with Wordpress has mostly been in customising themes, working on performance. The bulk of my work is in the frontend so I thoroughly enjoyed the challenge of getting deeper into the "guts" of Wordpress and doing some back-end work.

## Process


I began by creating a small "use case" test for each element of functionality that I was unfamiliar with. I did this by constructing very basic, single file plugins using mostly procedural code. It quickly became clear that managing more than a small amount of functionality in this way would become hard to manage and debug.

In researching for a more robust way to put the plugin together I found the Wordpress Plugin Boilerplate which provided me with an opinionated structure to scaffold my project whilst also learning about object oriented approaches to Wordpress development which I would like to develop further.

I intend to continue refining this for my own learning and especially to nail down the rest authentication issues!

## Resources Used

- WordPress Plugin Boilerplate ( https://github.com/DevinVinson/WordPress-Plugin-Boilerplate )
- Admin Menus ( https://developer.wordpress.org/plugins/administration-menus/top-level-menus/ )

Wordpress Rest API Authentication:
- Authenticating Your WordPress Rest APIs - Gemma Black - Medium ( https://medium.com/@GemmaBlack/authenticating-your-wordpress-rest-apis-84d8775a6f87 )
- Authentication | REST API Handbook | WordPress Developer Resources ( https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/ )
- Wordpress rest api - CRUD example with a post - Netmidas ( https://netmidas.com/blog/wordpress-rest-api-crud-example-with-a-post/ )

Creating Custom Database Tables
- https://premium.wpmudev.org/blog/creating-database-tables-for-plugins/
- https://anchor.host/my-first-custom-table-with-wordpress/

## Decisions Made
Plugin Settings Menu/Page
- I decided to use a top-level menu to be easy to find for those asessing this task
- I chose to use VueJs to create the add/delete page in the admin section. I wanted to demonstrate that I have working knowledge of modern javascript frameworks and also wanted to learn how to integrate Vue with Wordpress. This was a limited approach which could be expanded upon by creating good workflow and implementing tooling and build processes
Rest API
- I chose to leave requests to the API as unauthenticated for all users as I could not get wp_nonce header authentication to work yet. This made me sad and I will continue to seek a solution (any tips gratefully received!). 

Styling
- Even though I am primarily a frontend developer I chose to leave the styling of the user-facing shortcode output to a minimum for two reasons:
  - to focus on the backend tasks which seem to be the main things being assessed on this challenge
  - and to allow theme defaults to apply more readily
 - I did, however place some CSS class names in the output to make for easy customisation later down the line or through the settings page


# Improvements/To Do
- API request security
- Improved input validation
- Improved responsivity
- Option to 'update' records in the settings page
- Better UX for the shortcode output (sorting by rating, brand, links to brand website, etc)


Looking forward to your feedback.


