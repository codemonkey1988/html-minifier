# html-minifier
Minifies the TYPO3 output in frontend.

Can be configured via TypoScript constants

```
plugin.tx_htmlminifier {
	enable = 1
	remove_comments = 1
	remove_all_whitespaces = 1
	keep_typo3_header_comment = 1
}
```