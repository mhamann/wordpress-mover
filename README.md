The Wordpress Mover Script
==========================
Quickly and easily move your existing Wordpress installation to a new directory or domain.

Why? What's this?
-----------------
If you're like me, you've got a development server on which you do most of your initial WP work. So, as more and more web developers turn to WordPress as a viable CMS or site platform, it becomes readily apparent that WordPress’s practice of putting static ur'’s in your database is going to cause headaches down the road–when it's time to move. For seasoned developer or SQL guru, this isn't *too* much of a problem. Just run a few queries against the WP database, then move both the DB and the site files to the new server. But what about those less code-savvy or just plain-lazy people who also code WP-based sites? Editing raw SQL tables can be an inconvenient and daunting task.

Enter the Wordpress Mover.

How to Use
------------
1. Upload wp-mover.php to your WordPress root. (Actually, you can put this file anywhere on your server, but it’ll auto-load your database config info if it’s in the WP root).
2. Navigate to the URL where you installed wp-mover.php and ensure all the fields are filled in.
3. Click “Run Mover” and a few seconds later, you’re done!

4. You definitely want to to remove the script (or at least disable all permissions on it) when you're finished. If not, dirty hackers and other nefarious individuals will have access to your Wordpress database credentials and will probably wreak unrestrained havoc!