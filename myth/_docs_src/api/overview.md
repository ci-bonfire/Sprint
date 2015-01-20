# API Tools Overview
Sprint provides a handful of tools for building a JSON-based API server and makes it easy to setup and use. 

## Essential Components
The following are a list of the components that are provided to help you build out your API. 

- An [API  Controller](api/controller) with helper methods for return JSON-based success/failure messages and paginating results in a consistent manner.
- Additional Authentication classes, based on [HTTP Basic](api/httpbasic), [HTTP Digest](api/httpdigest), and [oAuth 2](api/oauth) authentication standards, all with built-in throttling and logging. 

## Setting Up Your System
While the tools are all provided, you do need to run a generator that will create the proper migrations for you, and will even get you up and running with a basic API Admin area for you. 