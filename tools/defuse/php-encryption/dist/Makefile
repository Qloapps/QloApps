# This builds defuse-crypto.phar. To run this Makefile, `box` and `composer`
# must be installed and in your $PATH. Run it from inside the dist/ directory.

box := $(shell which box)
composer := "composer"

.PHONY: all
all: build-phar

.PHONY: sign-phar
sign-phar:
	gpg -u 7B4B2D98 --armor --output defuse-crypto.phar.sig --detach-sig defuse-crypto.phar

# ensure we run in clean tree. export git tree and run there.
.PHONY: build-phar
build-phar:
	@echo "Creating .phar from revision $(shell git rev-parse HEAD)."
	rm -rf worktree
	install -d worktree
	(cd $(CURDIR)/..; git archive HEAD) | tar -x -C worktree
	$(MAKE) -f $(CURDIR)/Makefile -C worktree defuse-crypto.phar
	mv worktree/*.phar .
	rm -rf worktree

.PHONY: clean
clean:
	rm -vf defuse-crypto.phar defuse-crypto.phar.sig

# Inside workdir/:

defuse-crypto.phar: dist/box.json composer.lock
	cp dist/box.json .
	php -d phar.readonly=0 $(box) build -c box.json -v

composer.lock:
	$(composer) install --no-dev

