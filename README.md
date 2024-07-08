# HidePrefix

![ci](https://github.com/gesinn-it-pub/mediawiki-extensions-HidePrefix/actions/workflows/ci.yml/badge.svg)
[![codecov](https://codecov.io/github/gesinn-it-pub/mediawiki-extensions-HidePrefix/branch/master/graph/badge.svg?token=pdUWtEQ8rB)](https://codecov.io/github/gesinn-it-pub/mediawiki-extensions-HidePrefix)

The HidePrefix extension hides prefix in links and page titles.

For example, wikitext `[[Help:FAQ]]' normally results in link `Help:FAQ'. If this extension is
enabled, result will be just `FAQ' without the `Help:' prefix.

The same is about page titles. Page `FAQ' in `Help' namespace normally has `Help:FAQ' title. If this
extension is enabled, visible page title will be just `FAQ' without the `Help:' prefix.

## Download

 git clone https://github.com/gesinn-it-pub/mediawiki-extensions-HidePrefix

## Installation

To install this extension, add the following to LocalSettings.php:

- wfLoadExtension("HidePrefix");

## License

GNU Affero General Public License, version 3 or any later version. See AGPL-3.0.txt file for the
full license text.

## See also

*   Extension HideNamespace - This extension allows drop prefix in page title and provides control
    (e. g. hide prefix only in pages of specific namespace, or control prefix visibility from within
    page content), but it does not affect links.

## Links

* Extension page: https://www.mediawiki.org/wiki/Extension:HidePrefix
* Author page:    https://www.mediawiki.org/wiki/User:Van_de_Bugger
* License page:   https://www.gnu.org/licenses/agpl.html
