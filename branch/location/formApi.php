{
  "fields": [
    {
      "name": "name",
      "label": "Full Name",
      "type": "text",
      "required": true,
      "validation": "string"
    },
    {
      "name": "email",
      "label": "Email Address",
      "type": "email",
      "required": true,
      "validation": "email"
    },
    {
      "name": "userType",
      "label": "User Type",
      "type": "select",
      "options": ["admin", "user"],
      "required": true
    },
    {
      "name": "adminCode",
      "label": "Admin Code",
      "type": "text",
      "required": false,
      "conditional": {
        "field": "userType",
        "value": "admin"
      }
    },
    {
      "name": "dob",
      "label": "Date of Birth",
      "type": "date",
      "required": false,
      "conditional": {
        "field": "userType",
        "value": "user"
      }
    }
  ]
}
