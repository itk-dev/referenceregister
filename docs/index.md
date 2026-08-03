# Referenceregister

``` mermaid
---
config:
  # theme: forest
  # fontFamily: serif
---
block
  columns 1

  block:page_add_entry
    columns 4
    title_add_entry["Add entry"]:4
    label_identifier["Identifier"]         input_name["test-123"]:3
    space:3 button_save("Save")

    class title_add_entry title
    class label_identifier label
    class input_identifier input
    class button_save button
  end

  space

  block:page_entry_added
    columns 1
    title_entry_added["Entry added"]
    message_entry_added["Entry added"]

    class title_entry_added title
    class message_entry_added message_success
  end

  page_add_entry --> page_entry_added

  classDef title fill:none,stroke:none
  classDef message fill:none,stroke:none
  classDef message_success fill:none,stroke:#afa
  classDef label fill:none,stroke:none,label-position:right
  classDef input fill:none,label-position:left, label-padding: 5
  classDef button fill:none
```

![Front page](images/front_page.png)

## Add entry

![Add entry](images/add_entry.png)
![Entry added](images/entry_added.png)

## Look up

![Look up "test"](images/look_up_test.png)
![Look up hit](images/look_up_hit.png)

![Look up "123"](images/look_up_123.png)
![Look up miss](images/look_up_miss.png)

## Remove entry

![Remove entry](images/remove_entry.png)
![Entry removed](images/entry_removed.png)

Not that we've "sucessfully" removed an non-existing entry.
