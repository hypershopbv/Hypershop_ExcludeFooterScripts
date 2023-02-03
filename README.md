## Goal
- Module to exclude javascript movement to the bottom of your page. This is very useful when using the JS setting: "Move JS code to the bottom of the page". This allows scripts to be executing immediately during page load.

## Installation / Setup
- Install the module using the command `composer require hypershop/module-exclude-footer-scripts-os`
- After installing, run a `bin/magento setup:upgrade` to add it to the Magento module list.

## Usage / Settings
- To prevent scripts to be moved to the bottom of your page, add the `excluded` tag to the script.

```javascript
<script type="text/javascript">
    console.log('I get moved to the footer after load');
</script>
```
```javascript
<script type="text/javascript" excluded>
    console.log('I do not get moved to the bottom!');
</script>
```

## Common issues
- None known so far.
