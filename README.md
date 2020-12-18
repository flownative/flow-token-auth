# Token based authentication for Neos Flow projects

## Installation

Run:

    composer require flownative/token-authentication    

## Usage

Run:

    ./flow hashtoken:createhashtoken --roleNames Neos.Neos:Editor
 
Provide token in your requests as request argument `_authenticationHashToken=<myToken>`.

Or as `Authorization` header with the value `Bearer <myToken>`.
