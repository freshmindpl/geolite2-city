# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [0.2.2] - 2017-02-09
### Changed
- Further improve notifications. Props @GaryJones

### Fixed
- Let user know when the database is already up to date. Props @GaryJones
- Fix typo. Props @GaryJones

## [0.2.1] - 2017-02-09
### Added
- Added integrity check to make sure the downloaded database file matches the expected MD5 hash.
- Added a mechanism to retry failed downloads three times before aborting.

### Changed
- All update operations work on temporary files until the download is confirmed to be good, to avoid breaking already working code on updates.

## [0.2.0] - 2016-08-01
### Added
- Added the path to the data to the Composer output.
- Added `LICENSE` file.

### Changed
- Changed license from GPL-v2.0+ to MIT.

## [0.1.6] - 2016-03-05
### Fixed
- Changed two constants that were now referencing the wrong class.

## [0.1.5] - 2016-03-04
### Fixed
- Split code into two different classes to avoid issues outside of Composer flow.

## [0.1.4] - 2016-03-04
### Fixed
- Corrected the `README.md` to adapt it to the recent changes and added example code.

## [0.1.3] - 2016-03-04
### Added
- Changed class into a Composer plugin to work around the fact that Composer does not call dependency scripts automatically.

## [0.1.2] - 2016-03-04
### Added
- The zipped file is now remove after it was unzipped, to recover storage space.

## [0.1.1] - 2016-03-04
### Added
- Added details about adding `scripts` hooks to `README.md`.

## [0.1.0] - 2016-03-03
### Added
- Initial release to GitHub.

[0.2.2]: https://github.com/brightnucleus/geolite2-country/compare/v0.2.1...v0.2.2
[0.2.1]: https://github.com/brightnucleus/geolite2-country/compare/v0.2.0...v0.2.1
[0.2.0]: https://github.com/brightnucleus/geolite2-country/compare/v0.1.6...v0.2.0
[0.1.6]: https://github.com/brightnucleus/geolite2-country/compare/v0.1.5...v0.1.6
[0.1.5]: https://github.com/brightnucleus/geolite2-country/compare/v0.1.4...v0.1.5
[0.1.4]: https://github.com/brightnucleus/geolite2-country/compare/v0.1.3...v0.1.4
[0.1.3]: https://github.com/brightnucleus/geolite2-country/compare/v0.1.2...v0.1.3
[0.1.2]: https://github.com/brightnucleus/geolite2-country/compare/v0.1.1...v0.1.2
[0.1.1]: https://github.com/brightnucleus/geolite2-country/compare/v0.1.0...v0.1.1
[0.1.0]: https://github.com/brightnucleus/geolite2-country/compare/v0.0.0...v0.1.0
