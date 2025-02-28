<?php

{
    "audience":  "urn:sso",
    "recipient": "http://localhost/sso/",
    "mappings": {
      "user_id":     "http://schemas.xmlsoap.org/ws/2005/05/identity/claims/nameidentifier",
      "email":       "http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress",
      "name":        "http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name",
      "given_name":  "http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname",
      "family_name": "http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname",
      "upn":         "http://schemas.xmlsoap.org/ws/2005/05/identity/claims/upn",
      "groups":      "http://schemas.xmlsoap.org/claims/Group"
    },
    "createUpnClaim":       true,
    "passthroughClaimsWithNoMapping": true,
    "mapUnknownClaimsAsIs": false,
    "mapIdentities":        true,
    "signatureAlgorithm":   "rsa-sha1",
    "digestAlgorithm":      "sha1",
    "destination":          "http://sso",
    "lifetimeInSeconds":    3600,
    "signResponse":         false,
    "typedAttributes":      true,
    "includeAttributeNameFormat":  true,
    "nameIdentifierFormat": "urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified",
    "nameIdentifierProbes": [
      "http://schemas.xmlsoap.org/ws/2005/05/identity/claims/nameidentifier",
      "http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress",
      "http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name"
    ],
    "authnContextClassRef": "urn:oasis:names:tc:SAML:2.0:ac:classes:unspecified",
    "logout": {
      "callback": "http://sso/logout",
      "slo_enabled": true
    },
    "binding": "urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"
    }


    