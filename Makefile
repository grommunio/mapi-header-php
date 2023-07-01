# -*- Makefile -*-

prefix = /usr
datadir = ${prefix}/share
pkgdatadir = ${datadir}/${PACKAGE_NAME}
PACKAGE_NAME = php-mapi
INSTALL = install -p
MKDIR_P = mkdir -p

all:

install:
	${MKDIR_P} ${DESTDIR}${pkgdatadir}
	${INSTALL} -m0644 *.php ${DESTDIR}${pkgdatadir}/
