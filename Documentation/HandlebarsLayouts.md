# Component: Handlebars Layouts

## Reference

<https://github.com/shannonmoeller/handlebars-layouts>

## Description

This is an implementation of [`handlebars-layouts`](https://github.com/shannonmoeller/handlebars-layouts)
that that allows to define layouts that can be extended with custom content.

The following Handlebars helpers currently exist:

| Helper    | Declaring class                                                 | Reference      |
| --------- | --------------------------------------------------------------- | -------------- |
| `extend`  | [`ExtendHelper`](../Classes/Renderer/Helper/ExtendHelper.php)   | [Reference][1] |
| `block`   | [`BlockHelper`](../Classes/Renderer/Helper/BlockHelper.php)     | [Reference][2] |
| `content` | [`ContentHelper`](../Classes/Renderer/Helper/ContentHelper.php) | [Reference][3] |

[1]: https://github.com/shannonmoeller/handlebars-layouts#extend-partial-context-keyvalue-
[2]: https://github.com/shannonmoeller/handlebars-layouts#block-name
[3]: https://github.com/shannonmoeller/handlebars-layouts#content-name-modeappendprependreplace
