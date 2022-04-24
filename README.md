ENGAGEBAY MODULE FOR MAGENTO2

1. Download the EngageBay Magento2 Module.
2. Extract the zip file. Copy the app folder and paste it in the Magento2 root folder. If prompted for
   Replace/Merge, click on Merge.
3. Go to Magento2 root folder and run the below commands:
   a. php bin/magento setup:upgrade
   b. php bin/magento setup:static-content:deploy –f
   c. php bin/magento cache:clean
   d. php bin/magento cache:flush
4. Login into admin area. Go to STORES->Configuration and Select EngageBay->Marketing.

5. Input the Credentials of EngageBay in Username and Password Fields and Connect.

6. If the Credentials are valid, you will observe the button as Connected else you will see an error with
   “Invalid Credentials”.

7. Go to Configuration options in the next section, Choose Yes/No if you wish to sync customer data,
   order data and to show web popups of EngageBay in the frontend area for the customers and Click
   on Save Config.

8. From the menu, Go to SYSTEM->Cache
   Management.

9. Click on Flush Magento Cache.

10. If you want to Import the Existing Magento Data like all the Customer data, all the Orders. You can
    Go to STORES->Configuration and Select EngageBay->Marketing. Click on “IMPORT ALL
    CUSTOMERS TO ENGAGEBAY” or “IMPORT ALL ORDERS TO ENGAGEBAY”.

According to the configuration options,

a. Register the customer from the frontend area/admin area and check if you are able to see
the registered customer as a contact in EngageBay.

b. Place the order from the frontend area and check if you are able to view the Order Details as
Notes for a contact in EngageBay.

c. You should be able to see the web popups in frontend area as per the configuration options.

d. Also you should be able to see the purchased products as tags for Contacts in EngageBay.
