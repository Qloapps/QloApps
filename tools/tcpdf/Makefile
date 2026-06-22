# Makefile
#
# @since       2026-04-21
# @category    Library
# @package     TCPDF
# @author      Nicola Asuni <info@tecnick.com>
# @copyright   2002-2026 Nicola Asuni - Tecnick.com LTD
# @license     https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
# @link        https://github.com/tecnickcom/TCPDF
#
# This file is part of tcpdf software library.
# ----------------------------------------------------------------------------------------------------------------------

SHELL=/bin/bash
.SHELLFLAGS=-o pipefail -c

# Project owner
OWNER=tecnickcom

# Project vendor
VENDOR=${OWNER}

# Project name
PROJECT=tcpdf

# Project version (strip trailing line endings and accidental literal "\\n")
VERSION=$(shell sed -E 's/\\n$$//' VERSION | tr -d '\r\n')

# Current directory
CURRENTDIR=$(dir $(realpath $(firstword $(MAKEFILE_LIST))))

# Target directory
TARGETDIR=$(CURRENTDIR)target

# sed argument for in-place substitutions
SEDINPLACE=-i
ifeq ($(shell uname -s),Darwin)
	SEDINPLACE=-i ''
endif

# Default port number for the example server
PORT?=8971

# PHP binary
PHP=$(shell which php)

# Composer executable
COMPOSER=$(PHP) -d "apc.enable_cli=0" $(shell which composer)

# --- MAKE TARGETS ---

# Display general help about this command
.PHONY: help
help:
	@echo ""
	@echo "$(PROJECT) Makefile."
	@echo "The following commands are available:"
	@echo ""
	@awk '/^## /{desc=substr($$0,4)} /^\.PHONY:/{if(NF>1) {target=$$2; if(desc) printf "  make %-15s: %s\n",target,desc; desc=""}}' Makefile
	@echo ""
	@echo "To test and build everything from scratch, use the shortcut:"
	@echo "    make x"
	@echo ""

# Alias for help target
.PHONY: all
all: help

# Test and build everything from scratch
.PHONY: x
x: buildall

## Test and build everything from scratch
.PHONY: buildall
buildall: deps
	$(MAKE) qa

## Delete vendor and generated directories
.PHONY: clean
clean:
	rm -rf ./vendor ./tests/vendor $(TARGETDIR) ./build ./cache

## Download dependencies for the library and test harness
.PHONY: deps
deps: ensuretarget
	$(COMPOSER) install --no-interaction
	@if [ -f ./tests/composer.json ]; then \
		$(COMPOSER) --working-dir=tests install --no-interaction; \
	fi

## Generate source code documentation with Doctum if available
.PHONY: doc
doc:
	@if [ -x ./vendor/bin/doctum ]; then \
		./vendor/bin/doctum update ./scripts/doctum.php --force; \
	else \
		echo "Doctum is not installed. Run make deps first."; \
		exit 1; \
	fi

## Create missing target directories for test and build artifacts
.PHONY: ensuretarget
ensuretarget:
	mkdir -p $(TARGETDIR)/test
	mkdir -p $(TARGETDIR)/report
	mkdir -p $(TARGETDIR)/doc

## Lint PHP files (syntax only)
.PHONY: lint
lint:
	find . -type f -name '*.php' \
		-not -path './vendor/*' \
		-not -path './tests/vendor/*' \
		-print0 | xargs -0 -n1 -P4 $(PHP) -l > /dev/null

## Run all checks
.PHONY: qa
qa: version ensuretarget lint test

## Generate quality reports (not implemented in this legacy repository)
.PHONY: report
report: ensuretarget
	@echo "No additional report target is configured for TCPDF."

## Start the development server
.PHONY: server
server:
	$(PHP) -t examples -S localhost:$(PORT)

## Tag this git version
.PHONY: tag
tag:
	git checkout main && \
	git tag -a ${VERSION} -m "Release ${VERSION}" && \
	git push origin --tags && \
	git pull

## Run integration tests from tests/launch.sh
.PHONY: test
test:
	XDEBUG_MODE=coverage sh ./tests/launch.sh

## Set the code version from the VERSION file
.PHONY: version
version:
	sed $(SEDINPLACE) -E "s#^([[:space:]]*private static [^=]+ = ')[^']*';#\1${VERSION}';#" include/tcpdf_static.php
	sed $(SEDINPLACE) -E "1,170 s#^// Version[[:space:]]+: .*#// Version     : ${VERSION}#" tcpdf.php
	sed $(SEDINPLACE) -E "1,170 s#^ \* @version .*# * @version ${VERSION}#" tcpdf.php

## Increase the version patch number
.PHONY: versionup
versionup:
	echo ${VERSION} | gawk -F. '{printf("%d.%d.%d\n",$$1,$$2,(($$3+1)));}' > VERSION
	$(MAKE) version
