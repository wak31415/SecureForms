# Serializing Forms for Storage

The forms will be saved as JSON in the following format:

```json

form = {
    "name": "This is a cool form",
    "elements": [
        {
            "name": auto generated,
            "type": ["radio","checkbox","text","longtext",...],
            "required": [true, false],
            "question": "Sample Question?",
            "options": [
                "Option 1",
                "Option 2",
                "Option 3"
            ],
            "random": [true, false],
        }
    ],
    "params": {}
    }
}
```

`"options"` can be empty (i.e. if `"type" == "text" || "longtext"`). It contains specifications for the answer possibilities. It's precise format depends on the cases below.

## Form question types

### "radio" and "checkbox"
- `"options"` contains the values of the available options to choose from
- `"random"` randomizes the order of the elements if set to true

### "text"