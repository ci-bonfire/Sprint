FORMAT: 1A
HOST: http://localhost/v1/


# Users

User-related resources in the API.


## Users Collection [/users]


### List Users [GET]

Lists all users in a paginated manner.

+ Response 200 (application/json)

        {
            "total": 1234,
            "first": 1,
            "last": 20,
            "prev_url": null,
            "next_url": "http://localhost//v1//users?page=2&per_page=20",
            "users": [
                {
                    "id":  1234,
"email":  "someone@example.com",
"username":  "Some default string",
"password_hash":  "Some default string",
"reset_hash":  "Some default string",
"activate_hash":  "Some default string",
"created_on":  "2015-03-14 05:24:45",
"status":  "Some default string",
"status_message":  "Some default string",
"active":  0,
"deleted":  0,
"force_pass_reset":  0,

                    "meta": {
                        "url": "http://api.example.com/v1/users/1
                    }
                }
            ]
        }

+ Response 404 (application/json)

        {
            "error": "resource_not_found",
            "error_description": "Unable to find any users that match your request."
        }



### Create a new User [POST]

Allows you to create a new User. The information should be submitted as a standard form submittal.

+ email (string) - The user's email address
+ username (string) - The user's desired username
+ password (string) - The user's desired password
+ pass_confirm (string) - Verification of the password


+ Response 201 (application/json)

        {
            "id":  1234,
"email":  "someone@example.com",
"username":  "Some default string",
"password_hash":  "Some default string",
"reset_hash":  "Some default string",
"activate_hash":  "Some default string",
"created_on":  "2015-03-14 05:24:45",
"status":  "Some default string",
"status_message":  "Some default string",
"active":  0,
"deleted":  0,
"force_pass_reset":  0,

            "meta": {
                "url": "http://api.example.com/v1/users/1
            }
        }

+ Response 409 (application/json)

        {
            "error": "resource_exists",
            "error_description": "A User with that email already exists."
        }

+ Response 400 (application/json)

        {
            "error": "invalid_request",
            "error_description": "<p>The username is already in use on this site.</p>"
        }

+ Response 500 (application/json)

        {
            "error": "server_error",
            "error_description": "Unknown error creating user."
        }



## User [/users/{user_id}]

Users can have the following attributes:

- email
- username
- password
- pass_confirm
- status
- status_message
- active
- deleted

+ Parameters
    + user_id (required, number, `1`) ... An integer that is the ID of the user

### Get a Single User [GET]

+ Response 200 (application/json)

        {
            "id":  1234,
"email":  "someone@example.com",
"username":  "Some default string",
"password_hash":  "Some default string",
"reset_hash":  "Some default string",
"activate_hash":  "Some default string",
"created_on":  "2015-03-14 05:24:45",
"status":  "Some default string",
"status_message":  "Some default string",
"active":  0,
"deleted":  0,
"force_pass_reset":  0,

            "meta": {
            "url": "http://api.example.com/v1/users/1
            }
        }

+ Response 404 (application/json)

        {
            "error": "resource_not_found",
            "error_description": "Unable to find that user."
        }

+ Response 410 (application/json)

        {
            "error": "resource_gone",
            "error_description": "That user has been deleted."
        }



### Update a User [PUT]

+ Response 200 (application/json)

        {
            "id":  1234,
"email":  "someone@example.com",
"username":  "Some default string",
"password_hash":  "Some default string",
"reset_hash":  "Some default string",
"activate_hash":  "Some default string",
"created_on":  "2015-03-14 05:24:45",
"status":  "Some default string",
"status_message":  "Some default string",
"active":  0,
"deleted":  0,
"force_pass_reset":  0,

            "meta": {
                "url": "http://api.example.com/v1/users/1
            }
        }

+ Response 404 (application/json)

        {
            "error": "resource_not_found",
            "error_description": "Unable to find that User."
        }

+ Response 400 (application/json)

        {
            "error": "bad_request",
            "error_description": "No data found to update."
        }

+ Response 400 (application/json)

        {
            "error": "bad_request",
            "error_description": "<p>The username is already in use on this site.</p>"
        }

+ Response 500 (application/json)

        {
            "error": "server_error",
            "error_description": "Unknown error saving user."
        }



### Delete A User [DELETE]

+ Response 200 (application/json)

        {
            "response": "User was deleted"
        }

+ Response 404 (application/json)

        {
            "error": "resource_not_found",
            "error_description": "Unable to find that User."
        }

+ Response 500 (application/json)

        {
            "error": "server_error",
            "error_description": "Unknown database error."
        }


## Creation Form [/users/new]

### Get Form [GET]

Returns the form needed to create a new User.

+ Response 200 (text/html)

        <form action="">
            ...
        </form>



## Editing Form [/users/{user_id}/edit]

### Get Form [GET]

Returns the form needed to create a new User.

+ Response 200 (text/html)

        <form action="">
            ...
        </form>

+ Response 404 (application/json)

        {
            "error": "resource_not_found",
            "error_description": "User was not found."
        }

+ Response 410 (application/json)

        {
            "error": "resource_gone",
            "error_description": "That User has been deleted."
        }
