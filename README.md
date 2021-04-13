# WP Store finder

A wordpress plugin which displays a map with all the stores that are configured by there location.

Add your Google api key in your .env

`WP_STORE_FINDER_GOOGLE_API=[GOOGLE API]`

### Shortcode: 
`[wp-store-finder]`

### API:
#### Get all store
`/wp-json/wp-store-finder/v1`

### Import locations:

- Make a `json` directory withing your public root folder.
- make a `stores.json` file :
    - ```
        [
            {
                "name": "Store name",
                "street": "Lorem ipsumstreet",
                "number": "123",
                "zipcode": "1234 AB",
                "city": "Rotterdam",
                "position": {
                  "lat": [LATITUDE],
                  "lng": [LONGITUDE]
                }
            },
        ]
      ```
**Important**

This function is by default disabled. enable it by adding a .env prop called: 
`WP_STORE_FINDER_ACCEPT_IMPORT=true`



### Conribution is welcome :) 

###Jim van Eijk
_(jim@23g.nl)_
