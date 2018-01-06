CC=node_modules/.bin/coffee
ASSETDIR=assets

SRC=$(wildcard $(ASSETDIR)/*.coffee)
BUILD=$(SRC:%.coffee=%.js)

all: coffee

# coffeescript files

coffee: $(BUILD)

$(ASSETDIR)/%.js: $(ASSETDIR)/%.coffee
	$(CC) -m -c $<

# cleanup

.PHONY: clean
clean:
	-rm $(BUILD)


