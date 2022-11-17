# plausible-viewer
Embeds plausibe.io Analytics and displays plausible.io or self-hosted plausible iFrames in WordPress. 

## Setup
![Bildschirm­foto 2022-11-17 um 10 05 00](https://user-images.githubusercontent.com/2411246/202403155-a930949d-6d20-4bd7-aafb-df23242c4dd1.png)

## Dashboard
![Bildschirm­foto 2022-11-17 um 02 40 59](https://user-images.githubusercontent.com/2411246/202333073-9809decb-ff9f-4b69-ac8d-eea806e1f35f.png)

## Troubleshoot
If you have problems viewing the reports in WordPress, it may be that your  (self-hosted) Plausible instance does not allow embedding the repots as iFrame in other websites. Its based on the **Same-Origin_-Policy** of X-Frame-Options
(Look in your browsers Dev-Tools Console for error messages)

For this you have to enter the following in the ``/etc/nginx/sites-available/plausible.conf`` configuration:

````
location / {
  ...
  proxy_hide_header X-Frame-Options;
  ...
}
````

and restart your nginx-server.

## Selfhosting Plausible
I recommend running your own Plausible instance as a Droplet at DigitalOcean. My instance runs for $6/month and works great. You can choose the closest serverlocation (also in Europe) to be nice with the GDPR/DSGVO.

[![DigitalOcean Referral Badge](https://web-platforms.sfo2.digitaloceanspaces.com/WWW/Badge%202.svg)](https://www.digitalocean.com/?refcode=88059bbd7f27&utm_campaign=Referral_Invite&utm_medium=Referral_Program&utm_source=badge)

<span style="font-size:10px;">(Referral Link)</span>
