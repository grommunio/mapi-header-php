# -*- Makefile -*-

prefix = /usr
datadir = ${prefix}/share
pkgdatadir = ${datadir}/${PACKAGE_NAME}
PACKAGE_NAME = php-mapi
MKDIR_P = mkdir -p

all:

install:
	${MKDIR_P} ${DESTDIR}${pkgdatadir}
	install -m0644 *.php ${DESTDIR}${pkgdatadir}/
