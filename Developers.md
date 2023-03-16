# Developer Guide
This document describes how the flare scoreboard is implemented so new developers can have a better time working with the source code.

## Components
The following components exist in this library:

### HapiClient
Location: `src/HapiClient.php`

Description: This is the interface for communicating with any HAPI Server.
Its functions map to HAPI endpoints (catalog, info, data, etc).

### Http Client
Location: `src/Http.php`

Description: This fully static class provides helpers for Http requests.
This throws exceptions on failures so that users can expect their requests to be successful.

### JSON Wrapper
Location: `src/Json.php`

Description: A wrapper around PHP's built-in encode/decode functions.
The purpose of the wrapper is to throw exceptions on json decode failures.