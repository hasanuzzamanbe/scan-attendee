# WordPress Plugin Scan attendee

#### A simple plugin for WordPress to manage attendees for any event.

## Development
1. `npm i && npm run watch`
You will find js or Vue js setups on src directory.

## Production
1. `npm run production`
2. delete node_modules, src, package.lock, package.json
3. zip directory and upoload to WordPress plugin directory

## How to use:
Install and activate
 
Import your data to the database table by followings:
`attendee_id` 
`first_name`,
`last_name` 
`ticket_type` 
`email`,
`comment`,
`phone`
`username` 
`social`
`t_shirt_size`
`checkin` 
`breakfast`,
`lunch`,
`update_by` (integer)

[![Check Video Shorts](https://img.youtube.com/vi/BQ5uNkJKZAE/0.jpg)](https://www.youtube.com/watch?v=BQ5uNkJKZAE)

https://youtube.com/shorts/BQ5uNkJKZAE

Go to dashboard, scan QR or Input attendee_id manually. This will search attendee and you can manage check-in, breakfast, lunch or add new note about attendee.

If you have multiple volunteer to do that it will store volunteer user Id and show on the bottom who updated last.
