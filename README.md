## Goal
- Module to exclude javascript movement to the bottom of your page. This allows scripts to be executing immediately during page load.

## Installation / Setup
- Install the module using the command `composer require hypershop/module-exclude-footer-scripts-os`
- After installing, run a bin/magento setup:upgrade to add it to the Magento module list.

## Usage / Settings
- To prevent scripts to be moved to the bottom of your page, add the `excluded` tag to the script.

```
<script src="" type="text/javascript" excluded></script>
```

## Common issues
- None known so far.
