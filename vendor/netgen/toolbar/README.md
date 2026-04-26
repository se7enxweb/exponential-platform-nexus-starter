## Installation

To add the Netgen Toolbar to your Symfony project, use Composer for easy installation. Run the following command:

```bash
composer require netgen/toolbar
```

## Usage

### Configuration

By default, the toolbar uses the Ibexa admin siteaccess configured by the `%ngsite.admin_siteaccess_name%` parameter
available in Netgen Media Site. If you need some more control over the admin siteaccess which will be used, you can
use the configuration similar to the example below:

```yaml
netgen_toolbar:
    # Specifies the default admin siteaccess name, used when nothing else is configured
    default_admin_site_access: my_admin

    # Specifies the legacy siteaccess name, used when Toolbar detects the usage of Netgen Admin UI
    legacy_admin_site_access: legacy_admin

    # Specifies the map between siteaccesses/groups and admin siteaccess names to be used when current siteaccess
    # matches one of the keys in this list. In this list, siteaccesses have priority over groups.
    admin_site_access_mapping:
        fh_eng: fh_admin
        bold_eng: bold_admin
        bold_group: bold_admin
```

All of these configuration options are optional.

### Integration into templates

To use the Netgen Toolbar in your project, include it in your pagelayout template, directly after the opening `<body>` tag. Here is a basic example:

```twig
    {% include "@NetgenToolbar/ngtoolbar.html.twig" %}
```

### Adjusting for additional elements

If your layout includes elements like a sticky header that should be offset by the toolbar's height, you can specify additional CSS selectors. By default, only `#page` is adjusted. Here's how to include a sticky header in the offset calculation:

```twig
    {% include "@NetgenToolbar/ngtoolbar.html.twig" with {
        offset_selectors: [".site-header-sticky", "#page"]
    } %}
```

To specify that no elements should be offset, pass an empty array:

```twig
    {% include "@NetgenToolbar/ngtoolbar.html.twig" with {
        offset_selectors: []
    } %}
```

## Custom adjustments using CSS

For additional custom adjustments, a CSS variable `--ngtoolbar-height` is provided and can be used throughout your project's CSS as needed.

## How to mark elements that should be editable?

Each component or block item that is supposed to be editable needs to have 2 data parameters with content ID and location ID and one unique data parameter that indicates if it's a component or a block item.

```html
<article data-item="true" data-content-id="43" data-location-id="23">
  <!-- Your content here -->
</article>
```

or

```html
<article data-component="true" data-content-id="43" data-location-id="23">
  <!-- Your content here -->
</article>
```

### Helper macro for data parameters

The bundle includes a helper macros for adding necessary data parameters to components and block items. First, import the macro into your template:

```twig
    {% import "@NetgenToolbar/macros.html.twig" as toolbar_macros %}
```

Then, use it as follows:

#### Block item view types

```twig
    <article {{ toolbar_macros.item_params(content, location) }}>
        <!-- Your content here -->
    </article>
```

Rendered HTML:

```html
<article data-item="true" data-content-id="43" data-location-id="23">
  <!-- Your content here -->
</article>
```

#### Components

```twig
    <div {{ toolbar_macros.component_params(block) }}>
        <!-- Your content here -->
    </div>
```

Rendered HTML:

```html
<article data-component="true" data-content-id="43" data-location-id="23">
  <!-- Your content here -->
</article>
```

## Visibility conditions

The toolbar is displayed only to authenticated users who have the `ngtoolbar/use` Ibexa policy, ensuring that only authorized users can access toolbar functionalities.

## Building the project assets (for development purposes)

The Netgen Toolbar comes with its own set of assets. To build these assets for development or production environments, use the following commands:

### For development

```bash
    pnpm run dev
```

### For production

```bash
    pnpm run prod
```
