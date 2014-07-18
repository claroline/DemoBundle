Usage
=====

This bundle allows you to reinstall the platform periodically while displaying a counter allowing you to inform every users of the time remaining before it happens.

### Reinstalling the platform

In order to initialize the "time remaining" counter, you need to restart the platform once. Go into the DemoBundle/Resources/scripts directory and run:

    sudo php reinstall.php

### Setting the cron period

Setup a cron that fires the reinstall script periodically [doc](http://www.thegeekstuff.com/2011/07/cron-every-5-minutes/).

    sudo crontab -e

### Editing the bundle configuration file

Edit the Resources/config/parameters.yml file to reflect the cron period.

    claroline.demo_bundle.period: 7200 #seconds

### Updating the web folder

If you want the platform to redirect to a "reinstall" page when the script is working, run:

    php app/console claroline:demo:replace_web

### Displaying the counter

If you want to display the time remaining counter, edit the Resources/config/parameters.yml

    claroline.demo_bundle.display_counter: true

