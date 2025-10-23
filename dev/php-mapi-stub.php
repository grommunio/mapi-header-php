<?php
/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2025 grommunio GmbH
 */

// Guard to prevent multiple inclusions and conflicts with real MAPI extension
if (defined('MAPI_STUB_LOADED') || extension_loaded('mapi')) {
	return;
}
define('MAPI_STUB_LOADED', true);

class resource {}

/**
 * @param ?int $level
 * @return void
 */
function mapi_load_mapidefs(?int $level): void {
	return ;
}

/**
 * @return int
 */
function mapi_last_hresult(): int {
	return 0;
}

/**
 * @param int $proptag
 * @return int|bool
 */
function mapi_prop_type(int $proptag): int|bool {
	return 0;
}

/**
 * @param int $proptag
 * @return int|bool
 */
function mapi_prop_id(int $proptag): int|bool {
	return 0;
}

/**
 * @param int $errcode
 * @return bool
 */
function mapi_is_error(int $errcode): bool {
	return false;
}

/**
 * @param int $sev
 * @param int $code
 * @return int|bool
 */
function mapi_make_scode(int $sev, int $code): int|bool {
	return 0;
}

/**
 * @param int $proptype
 * @param int $propid
 * @return int|bool
 */
function mapi_prop_tag(int $proptype, int $propid): int|bool {
	return 0;
}

/**
 * @param ?string $displayname
 * @param string $type
 * @param string $address
 * @param ?int $flags
 * @return string|bool
 */
function mapi_createoneoff(?string $displayname, string $type, string $address, ?int $flags = 0): string|bool {
	return '';
}

/**
 * @param string $entryid
 * @return array|bool
 */
function mapi_parseoneoff(string $entryid): array|bool {
	return [];
}

/**
 * @param string $username
 * @param string $password
 * @param ?string $server
 * @param ?string $sslcert
 * @param ?string $sslpass
 * @param ?int $flags
 * @param ?string $wa_version
 * @param ?string $misc_version
 * @return resource|bool
 */
function mapi_logon_zarafa(string $username, string $password, ?string $server = null, ?string $sslcert = null, ?string $sslpass = null, ?int $flags = 0, ?string $wa_version = null, ?string $misc_version = null): resource|bool {
	return new resource();
}

/**
 * @param string $username
 * @param string $password
 * @param int $flags
 * @return resource|bool
 */
function mapi_logon_ex(string $username, string $password, int $flags): resource|bool {
	return new resource();
}

/**
 * @param string $username
 * @param int $flags
 * @return resource|bool
 */
function mapi_logon_np(string $username, int $flags): resource|bool {
	return new resource();
}

/**
 * @param string $token
 * @return resource|bool
 */
function mapi_logon_token(string $token): resource|bool {
	return new resource();
}

/**
 * @param resource $session
 * @return resource|bool
 */
function mapi_getmsgstorestable(resource $session): resource|bool {
	return new resource();
}

/**
 * @param resource $ses
 * @param string $entryid
 * @return resource|bool
 */
function mapi_openmsgstore(resource $ses, string $entryid): resource|bool {
	return new resource();
}

/**
 * @param resource $ses
 * @param string $uid
 * @return resource|bool
 */
function mapi_openprofilesection(resource $ses, string $uid): resource|bool {
	return new resource();
}

/**
 * @param resource $session
 * @return resource|bool
 */
function mapi_openaddressbook(resource $session): resource|bool {
	return new resource();
}

/**
 * @param resource $ses
 * @param ?string $entryid
 * @param ?int $flags
 * @return resource|bool
 */
function mapi_openentry(resource $ses, ?string $entryid = null, ?int $flags = 0): resource|bool {
	return new resource();
}

/**
 * @param resource $abk
 * @param ?string $entryid
 * @param ?int $flags
 * @return resource|bool
 */
function mapi_ab_openentry(resource $abk, ?string $entryid = null, ?int $flags = 0): resource|bool {
	return new resource();
}

/**
 * @param resource $abk
 * @param array $names
 * @param ?int $flags
 * @return mixed
 */
function mapi_ab_resolvename(resource $abk, array $names, ?int $flags = 0): mixed {
	return null;
}

/**
 * @param resource $abk
 * @return string|bool
 */
function mapi_ab_getdefaultdir(resource $abk): string|bool {
	return '';
}

/**
 * @param resource $store
 * @param string $mailbox_dn
 * @return string|bool
 */
function mapi_msgstore_createentryid(resource $store, string $mailbox_dn): string|bool {
	return '';
}

/**
 * @param resource $store
 * @param string $user
 * @param string $server
 * @return bool
 */
function mapi_msgstore_getarchiveentryid(resource $store, string $user, string $server): bool {
	return false;
}

/**
 * @param resource $store
 * @param ?string $entryid
 * @param ?int $flags
 * @return resource|bool
 */
function mapi_msgstore_openentry(resource $store, ?string $entryid = null, ?int $flags = 0): resource|bool {
	return new resource();
}

/**
 * @param resource $store
 * @return resource|bool
 */
function mapi_msgstore_getreceivefolder(resource $store): resource|bool {
	return new resource();
}

/**
 * @param resource $store
 * @param string $sk_fld
 * @param ?string $sk_msg
 * @return string|bool
 */
function mapi_msgstore_entryidfromsourcekey(resource $store, string $sk_fld, ?string $sk_msg = null): string|bool {
	return '';
}

/**
 * @param resource $store
 * @param string $entryid
 * @param int $event_mask
 * @param resource $sink
 * @return int|bool
 */
function mapi_msgstore_advise(resource $store, string $entryid, int $event_mask, resource $sink): int|bool {
	return 0;
}

/**
 * @param resource $store
 * @param int $sub_id
 * @return bool
 */
function mapi_msgstore_unadvise(resource $store, int $sub_id): bool {
	return false;
}

/**
 * @param ?resource $store
 * @param ?string $entryid
 * @return bool
 */
function mapi_msgstore_abortsubmit(?resource $store, ?string $entryid = null): bool {
	return false;
}

/**
 * @return resource|bool
 */
function mapi_sink_create(): resource|bool {
	return new resource();
}

/**
 * @param resource $sink
 * @param int $time
 * @return mixed
 */
function mapi_sink_timedwait(resource $sink, int $time): mixed {
	return null;
}

/**
 * @param resource $table
 * @param ?array $proptags
 * @param ?array $restrict
 * @return mixed
 */
function mapi_table_queryallrows(resource $table, ?array $proptags = null, ?array $restrict = null): mixed {
	return null;
}

/**
 * @param resource $table
 * @param ?array $proptags
 * @param ?int $start
 * @param ?int $limit
 * @return mixed
 */
function mapi_table_queryrows(resource $table, ?array $proptags = null, ?int $start = 0, ?int $limit = 0): mixed {
	return null;
}

/**
 * @param resource $table
 * @return int|bool
 */
function mapi_table_getrowcount(resource $table): int|bool {
	return 0;
}

/**
 * @param resource $table
 * @param array $columns
 * @param ?int $flags
 * @return bool
 */
function mapi_table_setcolumns(resource $table, array $columns, ?int $flags = 0): bool {
	return false;
}

/**
 * @param resource $table
 * @param int $bookmark
 * @param int $rowcount
 * @return int|bool
 */
function mapi_table_seekrow(resource $table, int $bookmark, int $rowcount): int|bool {
	return 0;
}

/**
 * @param resource $table
 * @param array $sortcrit
 * @param ?int $flags
 * @return bool
 */
function mapi_table_sort(resource $table, array $sortcrit, ?int $flags = 0): bool {
	return false;
}

/**
 * @param resource $table
 * @param array $restrict
 * @param ?int $flags
 * @return bool
 */
function mapi_table_restrict(resource $table, array $restrict, ?int $flags = 0): bool {
	return false;
}

/**
 * @param resource $table
 * @param array $restrict
 * @param ?int $bookmark
 * @param ?int $flags
 * @return int|bool
 */
function mapi_table_findrow(resource $table, array $restrict, ?int $bookmark = 0, ?int $flags = 0): int|bool {
	return 0;
}

/**
 * @param resource $table
 * @return int|bool
 */
function mapi_table_createbookmark(resource $table): int|bool {
	return 0;
}

/**
 * @param resource $table
 * @param int $bookmark
 * @return bool
 */
function mapi_table_freebookmark(resource $table, int $bookmark): bool {
	return false;
}

/**
 * @param resource $fld
 * @param ?int $flags
 * @return resource|bool
 */
function mapi_folder_gethierarchytable(resource $fld, ?int $flags = 0): resource|bool {
	return new resource();
}

/**
 * @param resource $fld
 * @param ?int $flags
 * @return resource|bool
 */
function mapi_folder_getcontentstable(resource $fld, ?int $flags = 0): resource|bool {
	return new resource();
}

/**
 * @param resource $fld
 * @return resource|bool
 */
function mapi_folder_getrulestable(resource $fld): resource|bool {
	return new resource();
}

/**
 * @param resource $fld
 * @param ?int $flags
 * @return resource|bool
 */
function mapi_folder_createmessage(resource $fld, ?int $flags = 0): resource|bool {
	return new resource();
}

/**
 * @param resource $fld
 * @param string $fname
 * @param ?string $comment
 * @param ?int $flags
 * @param ?int $folder_type
 * @return resource|bool
 */
function mapi_folder_createfolder(resource $fld, string $fname, ?string $comment = null, ?int $flags = 0, ?int $folder_type = 0): resource|bool {
	return new resource();
}

/**
 * @param resource $fld
 * @param array $entryids
 * @param ?int $flags
 * @return bool
 */
function mapi_folder_deletemessages(resource $fld, array $entryids, ?int $flags = 0): bool {
	return false;
}

/**
 * @param resource $srcfld
 * @param array $entryids
 * @param resource $dstfld
 * @param ?int $flags
 * @return bool
 */
function mapi_folder_copymessages(resource $srcfld, array $entryids, resource $dstfld, ?int $flags = 0): bool {
	return false;
}

/**
 * @param resource $fld
 * @param ?int $flags
 * @return bool
 */
function mapi_folder_emptyfolder(resource $fld, ?int $flags = 0): bool {
	return false;
}

/**
 * @param resource $srcfld
 * @param string $entryid
 * @param resource $dstfld
 * @param ?string $name
 * @param ?int $flags
 * @return bool
 */
function mapi_folder_copyfolder(resource $srcfld, string $entryid, resource $dstfld, ?string $name, ?int $flags = 0): bool {
	return false;
}

/**
 * @param resource $fld
 * @param string $entryid
 * @param ?int $flags
 * @return bool
 */
function mapi_folder_deletefolder(resource $fld, string $entryid, ?int $flags = 0): bool {
	return false;
}

/**
 * @param resource $fld
 * @param array $entryids
 * @param ?int $flags
 * @return bool
 */
function mapi_folder_setreadflags(resource $fld, array $entryids, ?int $flags = 0): bool {
	return false;
}

/**
 * @param resource $fld
 * @param array $restriction
 * @param array $folderlist
 * @param int $flags
 * @return bool
 */
function mapi_folder_setsearchcriteria(resource $fld, array $restriction, array $folderlist, int $flags): bool {
	return false;
}

/**
 * @param resource $fld
 * @param ?int $flags
 * @return mixed
 */
function mapi_folder_getsearchcriteria(resource $fld, ?int $flags = 0): mixed {
	return null;
}

/**
 * @param resource $fld
 * @param array $rows
 * @param ?int $flags
 * @return bool
 */
function mapi_folder_modifyrules(resource $fld, array $rows, ?int $flags = 0): bool {
	return false;
}

/**
 * @param resource $msg
 * @return resource|bool
 */
function mapi_message_getattachmenttable(resource $msg): resource|bool {
	return new resource();
}

/**
 * @param resource $msg
 * @return resource|bool
 */
function mapi_message_getrecipienttable(resource $msg): resource|bool {
	return new resource();
}

/**
 * @param resource $msg
 * @param int $id
 * @return resource|bool
 */
function mapi_message_openattach(resource $msg, int $id): resource|bool {
	return new resource();
}

/**
 * @param resource $msg
 * @param ?int $flags
 * @return resource|bool
 */
function mapi_message_createattach(resource $msg, ?int $flags = 0): resource|bool {
	return new resource();
}

/**
 * @param resource $msg
 * @param int $id
 * @param ?int $flags
 * @return bool
 */
function mapi_message_deleteattach(resource $msg, int $id = 0, ?int $flags = 0): bool {
	return false;
}

/**
 * @param resource $msg
 * @param int $flags
 * @param array $adrlist
 * @return bool
 */
function mapi_message_modifyrecipients(resource $msg, int $flags, array $adrlist): bool {
	return false;
}

/**
 * @param resource $msg
 * @return bool
 */
function mapi_message_submitmessage(resource $msg): bool {
	return false;
}

/**
 * @param resource $msg
 * @param int $flags
 * @return bool
 */
function mapi_message_setreadflag(resource $msg, int $flags): bool {
	return false;
}

/**
 * @param resource $any
 * @param int $proptag
 * @param ?int $flags
 * @param ?string $guid
 * @return resource|bool
 */
function mapi_openpropertytostream(resource $any, int $proptag, ?int $flags = 0, ?string $guid = null): resource|bool {
	return new resource();
}

/**
 * @param resource $stream
 * @param string $data
 * @return int|bool
 */
function mapi_stream_write(resource $stream, string $data): int|bool {
	return 0;
}

/**
 * @param resource $stream
 * @param int $size
 * @return string|bool
 */
function mapi_stream_read(resource $stream, int $size): string|bool {
	return '';
}

/**
 * @param resource $stream
 * @return array|bool
 */
function mapi_stream_stat(resource $stream): array|bool {
	return [];
}

/**
 * @param resource $stream
 * @param int $offset
 * @param ?int $flags
 * @return bool
 */
function mapi_stream_seek(resource $stream, int $offset, ?int $flags = 0): bool {
	return false;
}

/**
 * @param resource $stream
 * @return bool
 */
function mapi_stream_commit(resource $stream): bool {
	return false;
}

/**
 * @param resource $stream
 * @param int $size
 * @return bool
 */
function mapi_stream_setsize(resource $stream, int $size): bool {
	return false;
}

/**
 * @return resource|bool
 */
function mapi_stream_create(): resource|bool {
	return new resource();
}

/**
 * @param resource $attach
 * @param ?int $flags
 * @return resource|bool
 */
function mapi_attach_openobj(resource $attach, ?int $flags = 0): resource|bool {
	return new resource();
}

/**
 * @param resource $any
 * @param ?int $flags
 * @return bool
 */
function mapi_savechanges(resource $any, ?int $flags = 0): bool {
	return false;
}

/**
 * @param resource $any
 * @param ?array $proptags
 * @return mixed
 */
function mapi_getprops(resource $any, ?array $proptags = null): mixed {
	return null;
}

/**
 * @param resource $any
 * @param array $propvals
 * @return bool
 */
function mapi_setprops(resource $any, array $propvals): bool {
	return false;
}

/**
 * @param resource $src
 * @param array $excliid
 * @param array $exclprop
 * @param resource $dst
 * @param ?int $flags
 * @return bool
 */
function mapi_copyto(resource $src, array $excliid, array $exclprop, resource $dst, ?int $flags = 0): bool {
	return false;
}

/**
 * @param resource $any
 * @param int $proptag
 * @param ?string $iid
 * @param ?int $interfaceflags
 * @param ?int $flags
 * @return resource|bool
 */
function mapi_openproperty(resource $any, int $proptag, ?string $iid = null, ?int $interfaceflags = 0, ?int $flags = 0): resource|bool {
	return new resource();
}

/**
 * @param resource $any
 * @param array $proptags
 * @return bool
 */
function mapi_deleteprops(resource $any, array $proptags): bool {
	return false;
}

/**
 * @param resource $any
 * @param ?array $names
 * @return array|bool
 */
function mapi_getnamesfromids(resource $any, ?array $names = null): array|bool {
	return [];
}

/**
 * @param resource $store
 * @param array $names
 * @param ?array $guids
 * @return array|bool
 */
function mapi_getidsfromnames(resource $store, array $names, ?array $guids = null): array|bool {
	return [];
}

/**
 * @param string $data
 * @return string|bool
 */
function mapi_decompressrtf(string $data): string|bool {
	return '';
}

/**
 * @param resource $any
 * @param int $type
 * @return array|bool
 */
function mapi_zarafa_getpermissionrules(resource $any, int $type): array|bool {
	return [];
}

/**
 * @param resource $any
 * @param array $perms
 * @return bool
 */
function mapi_zarafa_setpermissionrules(resource $any, array $perms): bool {
	return false;
}

/**
 * @param resource $ses
 * @param string $entryid
 * @param int $start
 * @param int $end
 * @return array|bool
 */
function mapi_getuserfreebusy(resource $ses, string $entryid, int $start, int $end): array|bool {
	return [];
}

/**
 * @param resource $ses
 * @param string $entryid
 * @param int $start
 * @param int $end
 * @return string|bool
 */
function mapi_getuserfreebusyical(resource $ses, string $entryid, int $start, int $end): string|bool {
	return '';
}

/**
 * @param resource $e
 * @param resource $stream
 * @param int $flags
 * @param mixed $i
 * @param mixed $restrict
 * @param mixed $inclprop
 * @param mixed $exclprop
 * @param int $bufsize
 * @return bool
 */
function mapi_exportchanges_config(resource $e, resource $stream, int $flags, mixed $i, mixed $restrict, mixed $inclprop, mixed $exclprop, int $bufsize): bool {
	return false;
}

/**
 * @param resource $x
 * @return mixed
 */
function mapi_exportchanges_synchronize(resource $x): mixed {
	return null;
}

/**
 * @param resource $e
 * @param resource $stream
 * @return bool
 */
function mapi_exportchanges_updatestate(resource $e, resource $stream): bool {
	return false;
}

/**
 * @param resource $r
 * @return int|bool
 */
function mapi_exportchanges_getchangecount(resource $r): int|bool {
	return 0;
}

/**
 * @param resource $i
 * @param resource $stream
 * @param int $flags
 * @return bool
 */
function mapi_importcontentschanges_config(resource $i, resource $stream, int $flags): bool {
	return false;
}

/**
 * @param resource $i
 * @param ?resource $stream
 * @return bool
 */
function mapi_importcontentschanges_updatestate(resource $i, ?resource $stream = null): bool {
	return false;
}

/**
 * @param resource $i
 * @param array $props
 * @param int $flags
 * @param mixed &$msg
 * @return bool
 */
function mapi_importcontentschanges_importmessagechange(resource $i, array $props, int $flags, mixed &$msg): bool {
	return false;
}

/**
 * @param resource $i
 * @param int $flags
 * @param array $msgs
 * @return bool
 */
function mapi_importcontentschanges_importmessagedeletion(resource $i, int $flags, array $msgs): bool {
	return false;
}

/**
 * @param resource $i
 * @param array $readst
 * @return bool
 */
function mapi_importcontentschanges_importperuserreadstatechange(resource $i, array $readst): bool {
	return false;
}

/**
 * @param resource $r
 * @param string $a
 * @param string $b
 * @param string $c
 * @param string $d
 * @param string $e
 * @return bool
 */
function mapi_importcontentschanges_importmessagemove(resource $r, string $a, string $b, string $c, string $d, string $e): bool {
	return false;
}

/**
 * @param resource $i
 * @param resource $stream
 * @param int $flags
 * @return bool
 */
function mapi_importhierarchychanges_config(resource $i, resource $stream, int $flags): bool {
	return false;
}

/**
 * @param resource $i
 * @param ?resource $stream
 * @return bool
 */
function mapi_importhierarchychanges_updatestate(resource $i, ?resource $stream): bool {
	return false;
}

/**
 * @param resource $i
 * @param array $props
 * @return bool
 */
function mapi_importhierarchychanges_importfolderchange(resource $i, array $props): bool {
	return false;
}

/**
 * @param resource $i
 * @param int $flags
 * @param array $folders
 * @return bool
 */
function mapi_importhierarchychanges_importfolderdeletion(resource $i, int $flags, array $folders): bool {
	return false;
}

/**
 * @param object &$object
 * @return resource|bool
 */
function mapi_wrap_importcontentschanges(object &$object): resource|bool {
	return new resource();
}

/**
 * @param object &$object
 * @return resource|bool
 */
function mapi_wrap_importhierarchychanges(object &$object): resource|bool {
	return new resource();
}

/**
 * @param resource $ses
 * @param resource $abk
 * @param resource $msg
 * @param array $opts
 * @return resource|bool
 */
function mapi_inetmapi_imtoinet(resource $ses, resource $abk, resource $msg, array $opts): resource|bool {
	return new resource();
}

/**
 * @param resource $ses
 * @param resource $store
 * @param resource $abk
 * @param resource $msg
 * @param string $str
 * @param array $opts
 * @return bool
 */
function mapi_inetmapi_imtomapi(resource $ses, resource $store, resource $abk, resource $msg, string $str, array $opts): bool {
	return false;
}

/**
 * @param resource $ses
 * @param resource $store
 * @param resource $abk
 * @param resource $msg
 * @param string $str
 * @param bool $norecip
 * @return bool
 */
function mapi_icaltomapi(resource $ses, resource $store, resource $abk, resource $msg, string $str, bool $norecip): bool {
	return false;
}

/**
 * @param resource $abk
 * @param resource $fld
 * @param string $ics
 * @return array|bool
 */
function mapi_icaltomapi2(resource $abk, resource $fld, string $ics): array|bool {
	return [];
}

/**
 * @param resource $ses
 * @param resource $abk
 * @param resource $msg
 * @param array $opts
 * @return string|bool
 */
function mapi_mapitoical(resource $ses, resource $abk, resource $msg, array $opts): string|bool {
	return '';
}

/**
 * @param resource $ses
 * @param resource $store
 * @param resource $msg
 * @param string $str
 * @return bool
 */
function mapi_vcftomapi(resource $ses, resource $store, resource $msg, string $str): bool {
	return false;
}

/**
 * @param resource $fld
 * @param string $vcard
 * @return array|bool
 */
function mapi_vcftomapi2(resource $fld, string $vcard): array|bool {
	return [];
}

/**
 * @param resource $ses
 * @param resource $abk
 * @param resource $msg
 * @param array $opts
 * @return string|bool
 */
function mapi_mapitovcf(resource $ses, resource $abk, resource $msg, array $opts): string|bool {
	return '';
}

/**
 * @param string $cls
 * @return bool
 */
function mapi_enable_exceptions(string $cls): bool {
	return false;
}

/**
 * @param string $ft
 * @return bool
 */
function mapi_feature(string $ft): bool {
	return false;
}

/**
 * @param resource $ses
 * @param string &$data
 * @return int
 */
function kc_session_save(resource $ses, string &$data): int {
	return 0;
}

/**
 * @param mixed $data
 * @param mixed &$res
 * @return int
 */
function kc_session_restore(mixed $data, mixed &$res): int {
	return 0;
}

/**
 * @param string $username
 * @return array|bool
 */
function nsp_getuserinfo(string $username): array|bool {
	return [];
}

/**
 * @param string $username
 * @param string $oldpass
 * @param string $newpass
 * @return bool
 */
function nsp_setuserpasswd(string $username, string $oldpass, string $newpass): bool {
	return false;
}

/**
 * @param string $essdn
 * @return string|bool
 */
function nsp_essdn_to_username(string $essdn): string|bool {
	return '';
}

/**
 * @param resource $ses
 * @param ?string $srcheid
 * @param ?string $msgeid
 * @return mixed
 */
function mapi_linkmessage(resource $ses, ?string $srcheid = null, ?string $msgeid = null): mixed {
	return null;
}

/**
 * @param string $tz
 * @return string|bool
 */
function mapi_ianatz_to_tzdef(string $tz): string|bool {
	return '';
}

/**
 * @param int $code
 * @return string
 */
function mapi_strerror(int $code): string {
	return '';
}

if (!defined('MAPIDEFS_LOADED')) {
	define('MAPIDEFS_LOADED', 0x00000001);
}
if (!defined('PR_NULL')) {
	define('PR_NULL', 0x00000000);
}
if (!defined('PR_EMS_TEMPLATE_BLOB')) {
	define('PR_EMS_TEMPLATE_BLOB', 0x00010102);
}
if (!defined('PR_ACKNOWLEDGEMENT_MODE')) {
	define('PR_ACKNOWLEDGEMENT_MODE', 0x00010003);
}
if (!defined('PR_ALTERNATE_RECIPIENT_ALLOWED')) {
	define('PR_ALTERNATE_RECIPIENT_ALLOWED', 0x00020102);
}
if (!defined('PR_AUTHORIZING_USERS')) {
	define('PR_AUTHORIZING_USERS', 0x00030102);
}
if (!defined('PR_AUTO_FORWARD_COMMENT')) {
	define('PR_AUTO_FORWARD_COMMENT', 0x0004001E);
}
if (!defined('PR_EMS_SCRIPT_BLOB')) {
	define('PR_EMS_SCRIPT_BLOB', 0x00040102);
}
if (!defined('PR_AUTO_FORWARDED')) {
	define('PR_AUTO_FORWARDED', 0x0005000B);
}
if (!defined('PR_CONTENT_CONFIDENTIALITY_ALGORITHM_ID')) {
	define('PR_CONTENT_CONFIDENTIALITY_ALGORITHM_ID', 0x00060102);
}
if (!defined('PR_CONTENT_CORRELATOR')) {
	define('PR_CONTENT_CORRELATOR', 0x00070102);
}
if (!defined('PR_CONTENT_IDENTIFIER')) {
	define('PR_CONTENT_IDENTIFIER', 0x0008001E);
}
if (!defined('PR_CONTENT_LENGTH')) {
	define('PR_CONTENT_LENGTH', 0x00090003);
}
if (!defined('PR_CONTENT_RETURN_REQUESTED')) {
	define('PR_CONTENT_RETURN_REQUESTED', 0x000A000B);
}
if (!defined('PR_CONVERSATION_KEY')) {
	define('PR_CONVERSATION_KEY', 0x000B0102);
}
if (!defined('PR_CONVERSION_EITS')) {
	define('PR_CONVERSION_EITS', 0x000C0102);
}
if (!defined('PR_CONVERSION_WITH_LOSS_PROHIBITED')) {
	define('PR_CONVERSION_WITH_LOSS_PROHIBITED', 0x000D000B);
}
if (!defined('PR_CONVERTED_EITS')) {
	define('PR_CONVERTED_EITS', 0x000E0102);
}
if (!defined('PR_DEFERRED_DELIVERY_TIME')) {
	define('PR_DEFERRED_DELIVERY_TIME', 0x000F0040);
}
if (!defined('PR_DELIVER_TIME')) {
	define('PR_DELIVER_TIME', 0x00100040);
}
if (!defined('PR_DISCARD_REASON')) {
	define('PR_DISCARD_REASON', 0x00110003);
}
if (!defined('PR_DISCLOSURE_OF_RECIPIENTS')) {
	define('PR_DISCLOSURE_OF_RECIPIENTS', 0x0012000B);
}
if (!defined('PR_DL_EXPANSION_HISTORY')) {
	define('PR_DL_EXPANSION_HISTORY', 0x00130102);
}
if (!defined('PR_DL_EXPANSION_PROHIBITED')) {
	define('PR_DL_EXPANSION_PROHIBITED', 0x0014000B);
}
if (!defined('PR_EXPIRY_TIME')) {
	define('PR_EXPIRY_TIME', 0x00150040);
}
if (!defined('PR_IMPLICIT_CONVERSION_PROHIBITED')) {
	define('PR_IMPLICIT_CONVERSION_PROHIBITED', 0x0016000B);
}
if (!defined('PR_IMPORTANCE')) {
	define('PR_IMPORTANCE', 0x00170003);
}
if (!defined('PR_IPM_ID')) {
	define('PR_IPM_ID', 0x00180102);
}
if (!defined('PR_LATEST_DELIVERY_TIME')) {
	define('PR_LATEST_DELIVERY_TIME', 0x00190040);
}
if (!defined('PR_MESSAGE_CLASS')) {
	define('PR_MESSAGE_CLASS', 0x001A001E);
}
if (!defined('PR_MESSAGE_CLASS_A')) {
	define('PR_MESSAGE_CLASS_A', 0x001A001E);
}
if (!defined('PR_MESSAGE_DELIVERY_ID')) {
	define('PR_MESSAGE_DELIVERY_ID', 0x001B0102);
}
if (!defined('PR_MESSAGE_SECURITY_LABEL')) {
	define('PR_MESSAGE_SECURITY_LABEL', 0x001E0102);
}
if (!defined('PR_OBSOLETED_IPMS')) {
	define('PR_OBSOLETED_IPMS', 0x001F0102);
}
if (!defined('PR_ORIGINALLY_INTENDED_RECIPIENT_NAME')) {
	define('PR_ORIGINALLY_INTENDED_RECIPIENT_NAME', 0x00200102);
}
if (!defined('PR_ORIGINAL_EITS')) {
	define('PR_ORIGINAL_EITS', 0x00210102);
}
if (!defined('PR_ORIGINATOR_CERTIFICATE')) {
	define('PR_ORIGINATOR_CERTIFICATE', 0x00220102);
}
if (!defined('PR_ORIGINATOR_DELIVERY_REPORT_REQUESTED')) {
	define('PR_ORIGINATOR_DELIVERY_REPORT_REQUESTED', 0x0023000B);
}
if (!defined('PR_ORIGINATOR_RETURN_ADDRESS')) {
	define('PR_ORIGINATOR_RETURN_ADDRESS', 0x00240102);
}
if (!defined('PR_PARENT_KEY')) {
	define('PR_PARENT_KEY', 0x00250102);
}
if (!defined('PR_PRIORITY')) {
	define('PR_PRIORITY', 0x00260003);
}
if (!defined('PR_ORIGIN_CHECK')) {
	define('PR_ORIGIN_CHECK', 0x00270102);
}
if (!defined('PR_PROOF_OF_SUBMISSION_REQUESTED')) {
	define('PR_PROOF_OF_SUBMISSION_REQUESTED', 0x0028000B);
}
if (!defined('PR_READ_RECEIPT_REQUESTED')) {
	define('PR_READ_RECEIPT_REQUESTED', 0x0029000B);
}
if (!defined('PR_RECEIPT_TIME')) {
	define('PR_RECEIPT_TIME', 0x002A0040);
}
if (!defined('PR_RECIPIENT_REASSIGNMENT_PROHIBITED')) {
	define('PR_RECIPIENT_REASSIGNMENT_PROHIBITED', 0x002B000B);
}
if (!defined('PR_REDIRECTION_HISTORY')) {
	define('PR_REDIRECTION_HISTORY', 0x002C0102);
}
if (!defined('PR_RELATED_IPMS')) {
	define('PR_RELATED_IPMS', 0x002D0102);
}
if (!defined('PR_ORIGINAL_SENSITIVITY')) {
	define('PR_ORIGINAL_SENSITIVITY', 0x002E0003);
}
if (!defined('PR_LANGUAGES')) {
	define('PR_LANGUAGES', 0x002F001E);
}
if (!defined('PR_REPLY_TIME')) {
	define('PR_REPLY_TIME', 0x00300040);
}
if (!defined('PR_REPORT_TAG')) {
	define('PR_REPORT_TAG', 0x00310102);
}
if (!defined('PR_REPORT_TIME')) {
	define('PR_REPORT_TIME', 0x00320040);
}
if (!defined('PR_RETURNED_IPM')) {
	define('PR_RETURNED_IPM', 0x0033000B);
}
if (!defined('PR_SECURITY')) {
	define('PR_SECURITY', 0x00340003);
}
if (!defined('PR_INCOMPLETE_COPY')) {
	define('PR_INCOMPLETE_COPY', 0x0035000B);
}
if (!defined('PR_SENSITIVITY')) {
	define('PR_SENSITIVITY', 0x00360003);
}
if (!defined('PR_SUBJECT')) {
	define('PR_SUBJECT', 0x0037001E);
}
if (!defined('PR_SUBJECT_A')) {
	define('PR_SUBJECT_A', 0x0037001E);
}
if (!defined('PR_SUBJECT_IPM')) {
	define('PR_SUBJECT_IPM', 0x00380102);
}
if (!defined('PR_CLIENT_SUBMIT_TIME')) {
	define('PR_CLIENT_SUBMIT_TIME', 0x00390040);
}
if (!defined('PR_REPORT_NAME')) {
	define('PR_REPORT_NAME', 0x003A001E);
}
if (!defined('PR_SENT_REPRESENTING_SEARCH_KEY')) {
	define('PR_SENT_REPRESENTING_SEARCH_KEY', 0x003B0102);
}
if (!defined('PR_X400_CONTENT_TYPE')) {
	define('PR_X400_CONTENT_TYPE', 0x003C0102);
}
if (!defined('PR_SUBJECT_PREFIX')) {
	define('PR_SUBJECT_PREFIX', 0x003D001E);
}
if (!defined('PR_SUBJECT_PREFIX_A')) {
	define('PR_SUBJECT_PREFIX_A', 0x003D001E);
}
if (!defined('PR_NON_RECEIPT_REASON')) {
	define('PR_NON_RECEIPT_REASON', 0x003E0003);
}
if (!defined('PR_RECEIVED_BY_ENTRYID')) {
	define('PR_RECEIVED_BY_ENTRYID', 0x003F0102);
}
if (!defined('PR_RECEIVED_BY_NAME')) {
	define('PR_RECEIVED_BY_NAME', 0x0040001E);
}
if (!defined('PR_SENT_REPRESENTING_ENTRYID')) {
	define('PR_SENT_REPRESENTING_ENTRYID', 0x00410102);
}
if (!defined('PR_SENT_REPRESENTING_NAME')) {
	define('PR_SENT_REPRESENTING_NAME', 0x0042001E);
}
if (!defined('PR_SENT_REPRESENTING_NAME_A')) {
	define('PR_SENT_REPRESENTING_NAME_A', 0x0042001E);
}
if (!defined('PR_RCVD_REPRESENTING_ENTRYID')) {
	define('PR_RCVD_REPRESENTING_ENTRYID', 0x00430102);
}
if (!defined('PR_RCVD_REPRESENTING_NAME')) {
	define('PR_RCVD_REPRESENTING_NAME', 0x0044001E);
}
if (!defined('PR_RCVD_REPRESENTING_NAME_A')) {
	define('PR_RCVD_REPRESENTING_NAME_A', 0x0044001E);
}
if (!defined('PR_REPORT_ENTRYID')) {
	define('PR_REPORT_ENTRYID', 0x00450102);
}
if (!defined('PR_READ_RECEIPT_ENTRYID')) {
	define('PR_READ_RECEIPT_ENTRYID', 0x00460102);
}
if (!defined('PR_MESSAGE_SUBMISSION_ID')) {
	define('PR_MESSAGE_SUBMISSION_ID', 0x00470102);
}
if (!defined('PR_PROVIDER_SUBMIT_TIME')) {
	define('PR_PROVIDER_SUBMIT_TIME', 0x00480040);
}
if (!defined('PR_ORIGINAL_SUBJECT')) {
	define('PR_ORIGINAL_SUBJECT', 0x0049001E);
}
if (!defined('PR_DISC_VAL')) {
	define('PR_DISC_VAL', 0x004A000B);
}
if (!defined('PR_ORIG_MESSAGE_CLASS')) {
	define('PR_ORIG_MESSAGE_CLASS', 0x004B001E);
}
if (!defined('PR_ORIG_MESSAGE_CLASS_A')) {
	define('PR_ORIG_MESSAGE_CLASS_A', 0x004B001E);
}
if (!defined('PR_ORIGINAL_AUTHOR_ENTRYID')) {
	define('PR_ORIGINAL_AUTHOR_ENTRYID', 0x004C0102);
}
if (!defined('PR_ORIGINAL_AUTHOR_NAME')) {
	define('PR_ORIGINAL_AUTHOR_NAME', 0x004D001E);
}
if (!defined('PR_ORIGINAL_SUBMIT_TIME')) {
	define('PR_ORIGINAL_SUBMIT_TIME', 0x004E0040);
}
if (!defined('PR_REPLY_RECIPIENT_ENTRIES')) {
	define('PR_REPLY_RECIPIENT_ENTRIES', 0x004F0102);
}
if (!defined('PR_REPLY_RECIPIENT_NAMES')) {
	define('PR_REPLY_RECIPIENT_NAMES', 0x0050001E);
}
if (!defined('PR_RECEIVED_BY_SEARCH_KEY')) {
	define('PR_RECEIVED_BY_SEARCH_KEY', 0x00510102);
}
if (!defined('PR_RCVD_REPRESENTING_SEARCH_KEY')) {
	define('PR_RCVD_REPRESENTING_SEARCH_KEY', 0x00520102);
}
if (!defined('PR_READ_RECEIPT_SEARCH_KEY')) {
	define('PR_READ_RECEIPT_SEARCH_KEY', 0x00530102);
}
if (!defined('PR_REPORT_SEARCH_KEY')) {
	define('PR_REPORT_SEARCH_KEY', 0x00540102);
}
if (!defined('PR_ORIGINAL_DELIVERY_TIME')) {
	define('PR_ORIGINAL_DELIVERY_TIME', 0x00550040);
}
if (!defined('PR_ORIGINAL_AUTHOR_SEARCH_KEY')) {
	define('PR_ORIGINAL_AUTHOR_SEARCH_KEY', 0x00560102);
}
if (!defined('PR_MESSAGE_TO_ME')) {
	define('PR_MESSAGE_TO_ME', 0x0057000B);
}
if (!defined('PR_MESSAGE_CC_ME')) {
	define('PR_MESSAGE_CC_ME', 0x0058000B);
}
if (!defined('PR_MESSAGE_RECIP_ME')) {
	define('PR_MESSAGE_RECIP_ME', 0x0059000B);
}
if (!defined('PR_ORIGINAL_SENDER_NAME')) {
	define('PR_ORIGINAL_SENDER_NAME', 0x005A001E);
}
if (!defined('PR_ORIGINAL_SENDER_ENTRYID')) {
	define('PR_ORIGINAL_SENDER_ENTRYID', 0x005B0102);
}
if (!defined('PR_ORIGINAL_SENDER_SEARCH_KEY')) {
	define('PR_ORIGINAL_SENDER_SEARCH_KEY', 0x005C0102);
}
if (!defined('PR_ORIGINAL_SENT_REPRESENTING_NAME')) {
	define('PR_ORIGINAL_SENT_REPRESENTING_NAME', 0x005D001E);
}
if (!defined('PR_ORIGINAL_SENT_REPRESENTING_ENTRYID')) {
	define('PR_ORIGINAL_SENT_REPRESENTING_ENTRYID', 0x005E0102);
}
if (!defined('PR_ORIGINAL_SENT_REPRESENTING_SEARCH_KEY')) {
	define('PR_ORIGINAL_SENT_REPRESENTING_SEARCH_KEY', 0x005F0102);
}
if (!defined('PR_START_DATE')) {
	define('PR_START_DATE', 0x00600040);
}
if (!defined('PR_END_DATE')) {
	define('PR_END_DATE', 0x00610040);
}
if (!defined('PR_OWNER_APPT_ID')) {
	define('PR_OWNER_APPT_ID', 0x00620003);
}
if (!defined('PR_RESPONSE_REQUESTED')) {
	define('PR_RESPONSE_REQUESTED', 0x0063000B);
}
if (!defined('PR_SENT_REPRESENTING_ADDRTYPE')) {
	define('PR_SENT_REPRESENTING_ADDRTYPE', 0x0064001E);
}
if (!defined('PR_SENT_REPRESENTING_ADDRTYPE_A')) {
	define('PR_SENT_REPRESENTING_ADDRTYPE_A', 0x0064001E);
}
if (!defined('PR_SENT_REPRESENTING_EMAIL_ADDRESS')) {
	define('PR_SENT_REPRESENTING_EMAIL_ADDRESS', 0x0065001E);
}
if (!defined('PR_SENT_REPRESENTING_EMAIL_ADDRESS_A')) {
	define('PR_SENT_REPRESENTING_EMAIL_ADDRESS_A', 0x0065001E);
}
if (!defined('PR_ORIGINAL_SENDER_ADDRTYPE')) {
	define('PR_ORIGINAL_SENDER_ADDRTYPE', 0x0066001E);
}
if (!defined('PR_ORIGINAL_SENDER_EMAIL_ADDRESS')) {
	define('PR_ORIGINAL_SENDER_EMAIL_ADDRESS', 0x0067001E);
}
if (!defined('PR_ORIGINAL_SENT_REPRESENTING_ADDRTYPE')) {
	define('PR_ORIGINAL_SENT_REPRESENTING_ADDRTYPE', 0x0068001E);
}
if (!defined('PR_ORIGINAL_SENT_REPRESENTING_EMAIL_ADDRESS')) {
	define('PR_ORIGINAL_SENT_REPRESENTING_EMAIL_ADDRESS', 0x0069001E);
}
if (!defined('PR_CONVERSATION_TOPIC')) {
	define('PR_CONVERSATION_TOPIC', 0x0070001E);
}
if (!defined('PR_CONVERSATION_TOPIC_A')) {
	define('PR_CONVERSATION_TOPIC_A', 0x0070001E);
}
if (!defined('PR_CONVERSATION_INDEX')) {
	define('PR_CONVERSATION_INDEX', 0x00710102);
}
if (!defined('PR_ORIGINAL_DISPLAY_BCC')) {
	define('PR_ORIGINAL_DISPLAY_BCC', 0x0072001E);
}
if (!defined('PR_ORIGINAL_DISPLAY_CC')) {
	define('PR_ORIGINAL_DISPLAY_CC', 0x0073001E);
}
if (!defined('PR_ORIGINAL_DISPLAY_TO')) {
	define('PR_ORIGINAL_DISPLAY_TO', 0x0074001E);
}
if (!defined('PR_RECEIVED_BY_ADDRTYPE')) {
	define('PR_RECEIVED_BY_ADDRTYPE', 0x0075001E);
}
if (!defined('PR_RECEIVED_BY_EMAIL_ADDRESS')) {
	define('PR_RECEIVED_BY_EMAIL_ADDRESS', 0x0076001E);
}
if (!defined('PR_RCVD_REPRESENTING_ADDRTYPE')) {
	define('PR_RCVD_REPRESENTING_ADDRTYPE', 0x0077001E);
}
if (!defined('PR_RCVD_REPRESENTING_ADDRTYPE_A')) {
	define('PR_RCVD_REPRESENTING_ADDRTYPE_A', 0x0077001E);
}
if (!defined('PR_RCVD_REPRESENTING_EMAIL_ADDRESS')) {
	define('PR_RCVD_REPRESENTING_EMAIL_ADDRESS', 0x0078001E);
}
if (!defined('PR_RCVD_REPRESENTING_EMAIL_ADDRESS_A')) {
	define('PR_RCVD_REPRESENTING_EMAIL_ADDRESS_A', 0x0078001E);
}
if (!defined('PR_ORIGINAL_AUTHOR_ADDRTYPE')) {
	define('PR_ORIGINAL_AUTHOR_ADDRTYPE', 0x0079001E);
}
if (!defined('PR_ORIGINAL_AUTHOR_EMAIL_ADDRESS')) {
	define('PR_ORIGINAL_AUTHOR_EMAIL_ADDRESS', 0x007A001E);
}
if (!defined('PR_ORIGINALLY_INTENDED_RECIP_ADDRTYPE')) {
	define('PR_ORIGINALLY_INTENDED_RECIP_ADDRTYPE', 0x007B001E);
}
if (!defined('PR_ORIGINALLY_INTENDED_RECIP_EMAIL_ADDRESS')) {
	define('PR_ORIGINALLY_INTENDED_RECIP_EMAIL_ADDRESS', 0x007C001E);
}
if (!defined('PR_TRANSPORT_MESSAGE_HEADERS')) {
	define('PR_TRANSPORT_MESSAGE_HEADERS', 0x007D001E);
}
if (!defined('PR_TRANSPORT_MESSAGE_HEADERS_A')) {
	define('PR_TRANSPORT_MESSAGE_HEADERS_A', 0x007D001E);
}
if (!defined('PR_DELEGATION')) {
	define('PR_DELEGATION', 0x007E0102);
}
if (!defined('PR_TNEF_CORRELATION_KEY')) {
	define('PR_TNEF_CORRELATION_KEY', 0x007F0102);
}
if (!defined('PR_REPORT_DISPOSITION')) {
	define('PR_REPORT_DISPOSITION', 0x0080001E);
}
if (!defined('PR_REPORT_DISPOSITION_MODE')) {
	define('PR_REPORT_DISPOSITION_MODE', 0x0081001E);
}
if (!defined('PR_EMS_AB_ROOM_CAPACITY')) {
	define('PR_EMS_AB_ROOM_CAPACITY', 0x08070003);
}
if (!defined('PR_EMS_AB_ROOM_DESCRIPTION')) {
	define('PR_EMS_AB_ROOM_DESCRIPTION', 0x0809001E);
}
if (!defined('PR_CONTENT_INTEGRITY_CHECK')) {
	define('PR_CONTENT_INTEGRITY_CHECK', 0x0C000102);
}
if (!defined('PR_EXPLICIT_CONVERSION')) {
	define('PR_EXPLICIT_CONVERSION', 0x0C010003);
}
if (!defined('PR_IPM_RETURN_REQUESTED')) {
	define('PR_IPM_RETURN_REQUESTED', 0x0C02000B);
}
if (!defined('PR_MESSAGE_TOKEN')) {
	define('PR_MESSAGE_TOKEN', 0x0C030102);
}
if (!defined('PR_NDR_REASON_CODE')) {
	define('PR_NDR_REASON_CODE', 0x0C040003);
}
if (!defined('PR_NDR_DIAG_CODE')) {
	define('PR_NDR_DIAG_CODE', 0x0C050003);
}
if (!defined('PR_NON_RECEIPT_NOTIFICATION_REQUESTED')) {
	define('PR_NON_RECEIPT_NOTIFICATION_REQUESTED', 0x0C06000B);
}
if (!defined('PR_DELIVERY_POINT')) {
	define('PR_DELIVERY_POINT', 0x0C070003);
}
if (!defined('PR_ORIGINATOR_NON_DELIVERY_REPORT_REQUESTED')) {
	define('PR_ORIGINATOR_NON_DELIVERY_REPORT_REQUESTED', 0x0C08000B);
}
if (!defined('PR_ORIGINATOR_REQUESTED_ALTERNATE_RECIPIENT')) {
	define('PR_ORIGINATOR_REQUESTED_ALTERNATE_RECIPIENT', 0x0C090102);
}
if (!defined('PR_PHYSICAL_DELIVERY_BUREAU_FAX_DELIVERY')) {
	define('PR_PHYSICAL_DELIVERY_BUREAU_FAX_DELIVERY', 0x0C0A000B);
}
if (!defined('PR_PHYSICAL_DELIVERY_MODE')) {
	define('PR_PHYSICAL_DELIVERY_MODE', 0x0C0B0003);
}
if (!defined('PR_PHYSICAL_DELIVERY_REPORT_REQUEST')) {
	define('PR_PHYSICAL_DELIVERY_REPORT_REQUEST', 0x0C0C0003);
}
if (!defined('PR_PHYSICAL_FORWARDING_ADDRESS')) {
	define('PR_PHYSICAL_FORWARDING_ADDRESS', 0x0C0D0102);
}
if (!defined('PR_PHYSICAL_FORWARDING_ADDRESS_REQUESTED')) {
	define('PR_PHYSICAL_FORWARDING_ADDRESS_REQUESTED', 0x0C0E000B);
}
if (!defined('PR_PHYSICAL_FORWARDING_PROHIBITED')) {
	define('PR_PHYSICAL_FORWARDING_PROHIBITED', 0x0C0F000B);
}
if (!defined('PR_PHYSICAL_RENDITION_ATTRIBUTES')) {
	define('PR_PHYSICAL_RENDITION_ATTRIBUTES', 0x0C100102);
}
if (!defined('PR_PROOF_OF_DELIVERY')) {
	define('PR_PROOF_OF_DELIVERY', 0x0C110102);
}
if (!defined('PR_PROOF_OF_DELIVERY_REQUESTED')) {
	define('PR_PROOF_OF_DELIVERY_REQUESTED', 0x0C12000B);
}
if (!defined('PR_RECIPIENT_CERTIFICATE')) {
	define('PR_RECIPIENT_CERTIFICATE', 0x0C130102);
}
if (!defined('PR_RECIPIENT_NUMBER_FOR_ADVICE')) {
	define('PR_RECIPIENT_NUMBER_FOR_ADVICE', 0x0C14001E);
}
if (!defined('PR_RECIPIENT_TYPE')) {
	define('PR_RECIPIENT_TYPE', 0x0C150003);
}
if (!defined('PR_REGISTERED_MAIL_TYPE')) {
	define('PR_REGISTERED_MAIL_TYPE', 0x0C160003);
}
if (!defined('PR_REPLY_REQUESTED')) {
	define('PR_REPLY_REQUESTED', 0x0C17000B);
}
if (!defined('PR_REQUESTED_DELIVERY_METHOD')) {
	define('PR_REQUESTED_DELIVERY_METHOD', 0x0C180003);
}
if (!defined('PR_SENDER_ENTRYID')) {
	define('PR_SENDER_ENTRYID', 0x0C190102);
}
if (!defined('PR_SENDER_NAME')) {
	define('PR_SENDER_NAME', 0x0C1A001E);
}
if (!defined('PR_SENDER_NAME_A')) {
	define('PR_SENDER_NAME_A', 0x0C1A001E);
}
if (!defined('PR_SUPPLEMENTARY_INFO')) {
	define('PR_SUPPLEMENTARY_INFO', 0x0C1B001E);
}
if (!defined('PR_TYPE_OF_MTS_USER')) {
	define('PR_TYPE_OF_MTS_USER', 0x0C1C0003);
}
if (!defined('PR_SENDER_SEARCH_KEY')) {
	define('PR_SENDER_SEARCH_KEY', 0x0C1D0102);
}
if (!defined('PR_SENDER_ADDRTYPE')) {
	define('PR_SENDER_ADDRTYPE', 0x0C1E001E);
}
if (!defined('PR_SENDER_ADDRTYPE_A')) {
	define('PR_SENDER_ADDRTYPE_A', 0x0C1E001E);
}
if (!defined('PR_SENDER_EMAIL_ADDRESS')) {
	define('PR_SENDER_EMAIL_ADDRESS', 0x0C1F001E);
}
if (!defined('PR_SENDER_EMAIL_ADDRESS_A')) {
	define('PR_SENDER_EMAIL_ADDRESS_A', 0x0C1F001E);
}
if (!defined('PR_NDR_STATUS_CODE')) {
	define('PR_NDR_STATUS_CODE', 0x0C200003);
}
if (!defined('PR_DSN_REMOTE_MTA')) {
	define('PR_DSN_REMOTE_MTA', 0x0C21001E);
}
if (!defined('PR_CURRENT_VERSION')) {
	define('PR_CURRENT_VERSION', 0x0E000014);
}
if (!defined('PR_DELETE_AFTER_SUBMIT')) {
	define('PR_DELETE_AFTER_SUBMIT', 0x0E01000B);
}
if (!defined('PR_DISPLAY_BCC')) {
	define('PR_DISPLAY_BCC', 0x0E02001E);
}
if (!defined('PR_DISPLAY_BCC_A')) {
	define('PR_DISPLAY_BCC_A', 0x0E02001E);
}
if (!defined('PR_DISPLAY_CC')) {
	define('PR_DISPLAY_CC', 0x0E03001E);
}
if (!defined('PR_DISPLAY_CC_A')) {
	define('PR_DISPLAY_CC_A', 0x0E03001E);
}
if (!defined('PR_DISPLAY_TO')) {
	define('PR_DISPLAY_TO', 0x0E04001E);
}
if (!defined('PR_DISPLAY_TO_A')) {
	define('PR_DISPLAY_TO_A', 0x0E04001E);
}
if (!defined('PR_PARENT_DISPLAY')) {
	define('PR_PARENT_DISPLAY', 0x0E05001E);
}
if (!defined('PR_PARENT_DISPLAY_A')) {
	define('PR_PARENT_DISPLAY_A', 0x0E05001E);
}
if (!defined('PR_MESSAGE_DELIVERY_TIME')) {
	define('PR_MESSAGE_DELIVERY_TIME', 0x0E060040);
}
if (!defined('PR_MESSAGE_FLAGS')) {
	define('PR_MESSAGE_FLAGS', 0x0E070003);
}
if (!defined('PR_MESSAGE_SIZE')) {
	define('PR_MESSAGE_SIZE', 0x0E080003);
}
if (!defined('PR_MESSAGE_SIZE_EXTENDED')) {
	define('PR_MESSAGE_SIZE_EXTENDED', 0x0E080014);
}
if (!defined('PR_PARENT_ENTRYID')) {
	define('PR_PARENT_ENTRYID', 0x0E090102);
}
if (!defined('PR_PARENT_SVREID')) {
	define('PR_PARENT_SVREID', 0x0E0900FB);
}
if (!defined('PR_SENTMAIL_ENTRYID')) {
	define('PR_SENTMAIL_ENTRYID', 0x0E0A0102);
}
if (!defined('PR_CORRELATE')) {
	define('PR_CORRELATE', 0x0E0C000B);
}
if (!defined('PR_CORRELATE_MTSID')) {
	define('PR_CORRELATE_MTSID', 0x0E0D0102);
}
if (!defined('PR_DISCRETE_VALUES')) {
	define('PR_DISCRETE_VALUES', 0x0E0E000B);
}
if (!defined('PR_RESPONSIBILITY')) {
	define('PR_RESPONSIBILITY', 0x0E0F000B);
}
if (!defined('PR_SPOOLER_STATUS')) {
	define('PR_SPOOLER_STATUS', 0x0E100003);
}
if (!defined('PR_TRANSPORT_STATUS')) {
	define('PR_TRANSPORT_STATUS', 0x0E110003);
}
if (!defined('PR_MESSAGE_RECIPIENTS')) {
	define('PR_MESSAGE_RECIPIENTS', 0x0E12000D);
}
if (!defined('PR_MESSAGE_ATTACHMENTS')) {
	define('PR_MESSAGE_ATTACHMENTS', 0x0E13000D);
}
if (!defined('PR_SUBMIT_FLAGS')) {
	define('PR_SUBMIT_FLAGS', 0x0E140003);
}
if (!defined('PR_RECIPIENT_STATUS')) {
	define('PR_RECIPIENT_STATUS', 0x0E150003);
}
if (!defined('PR_TRANSPORT_KEY')) {
	define('PR_TRANSPORT_KEY', 0x0E160003);
}
if (!defined('PR_MSG_STATUS')) {
	define('PR_MSG_STATUS', 0x0E170003);
}
if (!defined('PR_MESSAGE_DOWNLOAD_TIME')) {
	define('PR_MESSAGE_DOWNLOAD_TIME', 0x0E180003);
}
if (!defined('PR_CREATION_VERSION')) {
	define('PR_CREATION_VERSION', 0x0E190014);
}
if (!defined('PR_MODIFY_VERSION')) {
	define('PR_MODIFY_VERSION', 0x0E1A0014);
}
if (!defined('PR_HASATTACH')) {
	define('PR_HASATTACH', 0x0E1B000B);
}
if (!defined('PR_BODY_CRC')) {
	define('PR_BODY_CRC', 0x0E1C0003);
}
if (!defined('PR_NORMALIZED_SUBJECT')) {
	define('PR_NORMALIZED_SUBJECT', 0x0E1D001E);
}
if (!defined('PR_NORMALIZED_SUBJECT_A')) {
	define('PR_NORMALIZED_SUBJECT_A', 0x0E1D001E);
}
if (!defined('PR_RTF_IN_SYNC')) {
	define('PR_RTF_IN_SYNC', 0x0E1F000B);
}
if (!defined('PR_ATTACH_SIZE')) {
	define('PR_ATTACH_SIZE', 0x0E200003);
}
if (!defined('PR_ATTACH_NUM')) {
	define('PR_ATTACH_NUM', 0x0E210003);
}
if (!defined('PR_PREPROCESS')) {
	define('PR_PREPROCESS', 0x0E22000B);
}
if (!defined('PR_INTERNET_ARTICLE_NUMBER')) {
	define('PR_INTERNET_ARTICLE_NUMBER', 0x0E230003);
}
if (!defined('PR_NEWSGROUP_NAME')) {
	define('PR_NEWSGROUP_NAME', 0x0E24001E);
}
if (!defined('PR_ORIGINATING_MTA_CERTIFICATE')) {
	define('PR_ORIGINATING_MTA_CERTIFICATE', 0x0E250102);
}
if (!defined('PR_PROOF_OF_SUBMISSION')) {
	define('PR_PROOF_OF_SUBMISSION', 0x0E260102);
}
if (!defined('PR_NT_SECURITY_DESCRIPTOR')) {
	define('PR_NT_SECURITY_DESCRIPTOR', 0x0E270102);
}
if (!defined('PR_PRIMARY_SEND_ACCOUNT')) {
	define('PR_PRIMARY_SEND_ACCOUNT', 0x0E28001E);
}
if (!defined('PR_NEXT_SEND_ACCT')) {
	define('PR_NEXT_SEND_ACCT', 0x0E29001E);
}
if (!defined('PR_TODO_ITEM_FLAGS')) {
	define('PR_TODO_ITEM_FLAGS', 0x0E2B0003);
}
if (!defined('PR_SWAPPED_TODO_STORE')) {
	define('PR_SWAPPED_TODO_STORE', 0x0E2C0102);
}
if (!defined('PR_SWAPPED_TODO_DATA')) {
	define('PR_SWAPPED_TODO_DATA', 0x0E2D0102);
}
if (!defined('PR_REPL_ITEMID')) {
	define('PR_REPL_ITEMID', 0x0E300003);
}
if (!defined('PR_REPL_CHANGENUM')) {
	define('PR_REPL_CHANGENUM', 0x0E330014);
}
if (!defined('PR_REPL_VERSIONHISTORY')) {
	define('PR_REPL_VERSIONHISTORY', 0x0E340102);
}
if (!defined('PR_REPL_FLAGS')) {
	define('PR_REPL_FLAGS', 0x0E380003);
}
if (!defined('PR_REPL_COPIEDFROM_VERSIONHISTORY')) {
	define('PR_REPL_COPIEDFROM_VERSIONHISTORY', 0x0E3C0102);
}
if (!defined('PR_REPL_COPIEDFROM_ITEMID')) {
	define('PR_REPL_COPIEDFROM_ITEMID', 0x0E3D0102);
}
if (!defined('PR_CREATOR_SID')) {
	define('PR_CREATOR_SID', 0x0E580102);
}
if (!defined('PR_LAST_MODIFIER_SID')) {
	define('PR_LAST_MODIFIER_SID', 0x0E590102);
}
if (!defined('PR_CI_SEARCH_ENABLED')) {
	define('PR_CI_SEARCH_ENABLED', 0x0E5C000B);
}
if (!defined('PR_READ')) {
	define('PR_READ', 0x0E69000B);
}
if (!defined('PR_NT_SECURITY_DESCRIPTOR_AS_XML')) {
	define('PR_NT_SECURITY_DESCRIPTOR_AS_XML', 0x0E6A001E);
}
if (!defined('PR_TRUST_SENDER')) {
	define('PR_TRUST_SENDER', 0x0E790003);
}
if (!defined('PR_EXTENDED_RULE_MSG_ACTIONS')) {
	define('PR_EXTENDED_RULE_MSG_ACTIONS', 0x0E990102);
}
if (!defined('PR_EXTENDED_RULE_MSG_CONDITION')) {
	define('PR_EXTENDED_RULE_MSG_CONDITION', 0x0E9A0102);
}
if (!defined('PR_EXTENDED_RULE_SIZE_LIMIT')) {
	define('PR_EXTENDED_RULE_SIZE_LIMIT', 0x0E9B0003);
}
if (!defined('PR_ASSOCIATED_SHARING_PROVIDER')) {
	define('PR_ASSOCIATED_SHARING_PROVIDER', 0x0EA00048);
}
if (!defined('PR_SEARCH_ATTACHMENTS')) {
	define('PR_SEARCH_ATTACHMENTS', 0x0EA5001E);
}
if (!defined('PR_SEARCH_RECIP_EMAIL_TO')) {
	define('PR_SEARCH_RECIP_EMAIL_TO', 0x0EA6001E);
}
if (!defined('PR_SEARCH_RECIP_EMAIL_CC')) {
	define('PR_SEARCH_RECIP_EMAIL_CC', 0x0EA7001E);
}
if (!defined('PR_SEARCH_RECIP_EMAIL_BCC')) {
	define('PR_SEARCH_RECIP_EMAIL_BCC', 0x0EA8001E);
}
if (!defined('PR_ACCESS')) {
	define('PR_ACCESS', 0x0FF40003);
}
if (!defined('PR_ROW_TYPE')) {
	define('PR_ROW_TYPE', 0x0FF50003);
}
if (!defined('PR_INSTANCE_KEY')) {
	define('PR_INSTANCE_KEY', 0x0FF60102);
}
if (!defined('PR_INSTANCE_SVREID')) {
	define('PR_INSTANCE_SVREID', 0x0FF600FB);
}
if (!defined('PR_ACCESS_LEVEL')) {
	define('PR_ACCESS_LEVEL', 0x0FF70003);
}
if (!defined('PR_MAPPING_SIGNATURE')) {
	define('PR_MAPPING_SIGNATURE', 0x0FF80102);
}
if (!defined('PR_RECORD_KEY')) {
	define('PR_RECORD_KEY', 0x0FF90102);
}
if (!defined('PR_STORE_RECORD_KEY')) {
	define('PR_STORE_RECORD_KEY', 0x0FFA0102);
}
if (!defined('PR_STORE_ENTRYID')) {
	define('PR_STORE_ENTRYID', 0x0FFB0102);
}
if (!defined('PR_MINI_ICON')) {
	define('PR_MINI_ICON', 0x0FFC0102);
}
if (!defined('PR_ICON')) {
	define('PR_ICON', 0x0FFD0102);
}
if (!defined('PR_OBJECT_TYPE')) {
	define('PR_OBJECT_TYPE', 0x0FFE0003);
}
if (!defined('PR_ENTRYID')) {
	define('PR_ENTRYID', 0x0FFF0102);
}
if (!defined('PR_BODY')) {
	define('PR_BODY', 0x1000001E);
}
if (!defined('PR_BODY_A')) {
	define('PR_BODY_A', 0x1000001E);
}
if (!defined('PR_BODY_W')) {
	define('PR_BODY_W', 0x1000001E);
}
if (!defined('PR_REPORT_TEXT')) {
	define('PR_REPORT_TEXT', 0x1001001E);
}
if (!defined('PR_ORIGINATOR_AND_DL_EXPANSION_HISTORY')) {
	define('PR_ORIGINATOR_AND_DL_EXPANSION_HISTORY', 0x10020102);
}
if (!defined('PR_REPORTING_DL_NAME')) {
	define('PR_REPORTING_DL_NAME', 0x10030102);
}
if (!defined('PR_REPORTING_MTA_CERTIFICATE')) {
	define('PR_REPORTING_MTA_CERTIFICATE', 0x10040102);
}
if (!defined('PR_RTF_SYNC_BODY_CRC')) {
	define('PR_RTF_SYNC_BODY_CRC', 0x10060003);
}
if (!defined('PR_RTF_SYNC_BODY_COUNT')) {
	define('PR_RTF_SYNC_BODY_COUNT', 0x10070003);
}
if (!defined('PR_RTF_SYNC_BODY_TAG')) {
	define('PR_RTF_SYNC_BODY_TAG', 0x1008001E);
}
if (!defined('PR_RTF_COMPRESSED')) {
	define('PR_RTF_COMPRESSED', 0x10090102);
}
if (!defined('PR_RTF_SYNC_PREFIX_COUNT')) {
	define('PR_RTF_SYNC_PREFIX_COUNT', 0x10100003);
}
if (!defined('PR_RTF_SYNC_TRAILING_COUNT')) {
	define('PR_RTF_SYNC_TRAILING_COUNT', 0x10110003);
}
if (!defined('PR_ORIGINALLY_INTENDED_RECIP_ENTRYID')) {
	define('PR_ORIGINALLY_INTENDED_RECIP_ENTRYID', 0x10120102);
}
if (!defined('PR_BODY_HTML')) {
	define('PR_BODY_HTML', 0x1013001E);
}
if (!defined('PR_BODY_HTML_A')) {
	define('PR_BODY_HTML_A', 0x1013001E);
}
if (!defined('PR_HTML')) {
	define('PR_HTML', 0x10130102);
}
if (!defined('PR_BODY_CONTENT_LOCATION')) {
	define('PR_BODY_CONTENT_LOCATION', 0x1014001E);
}
if (!defined('PR_BODY_CONTENT_LOCATION_A')) {
	define('PR_BODY_CONTENT_LOCATION_A', 0x1014001E);
}
if (!defined('PR_BODY_CONTENT_ID')) {
	define('PR_BODY_CONTENT_ID', 0x1015001E);
}
if (!defined('PR_BODY_CONTENT_ID_A')) {
	define('PR_BODY_CONTENT_ID_A', 0x1015001E);
}
if (!defined('PR_NATIVE_BODY_INFO')) {
	define('PR_NATIVE_BODY_INFO', 0x10160003);
}
if (!defined('PR_INTERNET_APPROVED')) {
	define('PR_INTERNET_APPROVED', 0x1030001E);
}
if (!defined('PR_INTERNET_CONTROL')) {
	define('PR_INTERNET_CONTROL', 0x1031001E);
}
if (!defined('PR_INTERNET_DISTRIBUTION')) {
	define('PR_INTERNET_DISTRIBUTION', 0x1032001E);
}
if (!defined('PR_INTERNET_FOLLOWUP_TO')) {
	define('PR_INTERNET_FOLLOWUP_TO', 0x1033001E);
}
if (!defined('PR_INTERNET_LINES')) {
	define('PR_INTERNET_LINES', 0x10340003);
}
if (!defined('PR_INTERNET_MESSAGE_ID')) {
	define('PR_INTERNET_MESSAGE_ID', 0x1035001E);
}
if (!defined('PR_INTERNET_MESSAGE_ID_A')) {
	define('PR_INTERNET_MESSAGE_ID_A', 0x1035001E);
}
if (!defined('PR_INTERNET_NEWSGROUPS')) {
	define('PR_INTERNET_NEWSGROUPS', 0x1036001E);
}
if (!defined('PR_INTERNET_ORGANIZATION')) {
	define('PR_INTERNET_ORGANIZATION', 0x1037001E);
}
if (!defined('PR_INTERNET_NNTP_PATH')) {
	define('PR_INTERNET_NNTP_PATH', 0x1038001E);
}
if (!defined('PR_INTERNET_REFERENCES')) {
	define('PR_INTERNET_REFERENCES', 0x1039001E);
}
if (!defined('PR_INTERNET_REFERENCES_A')) {
	define('PR_INTERNET_REFERENCES_A', 0x1039001E);
}
if (!defined('PR_SUPERSEDES')) {
	define('PR_SUPERSEDES', 0x103A001E);
}
if (!defined('PR_POST_FOLDER_ENTRIES')) {
	define('PR_POST_FOLDER_ENTRIES', 0x103B0102);
}
if (!defined('PR_POST_FOLDER_NAMES')) {
	define('PR_POST_FOLDER_NAMES', 0x103C001E);
}
if (!defined('PR_POST_REPLY_FOLDER_ENTRIES')) {
	define('PR_POST_REPLY_FOLDER_ENTRIES', 0x103D0102);
}
if (!defined('PR_POST_REPLY_FOLDER_NAMES')) {
	define('PR_POST_REPLY_FOLDER_NAMES', 0x103E001E);
}
if (!defined('PR_POST_REPLY_DENIED')) {
	define('PR_POST_REPLY_DENIED', 0x103F0102);
}
if (!defined('PR_NNTP_XREF')) {
	define('PR_NNTP_XREF', 0x1040001E);
}
if (!defined('PR_INTERNET_PRECEDENCE')) {
	define('PR_INTERNET_PRECEDENCE', 0x1041001E);
}
if (!defined('PR_IN_REPLY_TO_ID')) {
	define('PR_IN_REPLY_TO_ID', 0x1042001E);
}
if (!defined('PR_IN_REPLY_TO_ID_A')) {
	define('PR_IN_REPLY_TO_ID_A', 0x1042001E);
}
if (!defined('PR_LIST_HELP')) {
	define('PR_LIST_HELP', 0x1043001E);
}
if (!defined('PR_LIST_HELP_A')) {
	define('PR_LIST_HELP_A', 0x1043001E);
}
if (!defined('PR_LIST_SUBSCRIBE')) {
	define('PR_LIST_SUBSCRIBE', 0x1044001E);
}
if (!defined('PR_LIST_SUBSCRIBE_A')) {
	define('PR_LIST_SUBSCRIBE_A', 0x1044001E);
}
if (!defined('PR_LIST_UNSUBSCRIBE')) {
	define('PR_LIST_UNSUBSCRIBE', 0x1045001E);
}
if (!defined('PR_LIST_UNSUBSCRIBE_A')) {
	define('PR_LIST_UNSUBSCRIBE_A', 0x1045001E);
}
if (!defined('PR_INTERNET_RETURN_PATH')) {
	define('PR_INTERNET_RETURN_PATH', 0x1046001E);
}
if (!defined('PR_ICON_INDEX')) {
	define('PR_ICON_INDEX', 0x10800003);
}
if (!defined('PR_LAST_VERB_EXECUTED')) {
	define('PR_LAST_VERB_EXECUTED', 0x10810003);
}
if (!defined('PR_LAST_VERB_EXECUTION_TIME')) {
	define('PR_LAST_VERB_EXECUTION_TIME', 0x10820040);
}
if (!defined('PR_FLAG_STATUS')) {
	define('PR_FLAG_STATUS', 0x10900003);
}
if (!defined('PR_FLAG_COMPLETE_TIME')) {
	define('PR_FLAG_COMPLETE_TIME', 0x10910040);
}
if (!defined('PR_FOLLOWUP_ICON')) {
	define('PR_FOLLOWUP_ICON', 0x10950003);
}
if (!defined('PR_BLOCK_STATUS')) {
	define('PR_BLOCK_STATUS', 0x10960003);
}
if (!defined('PR_ITEM_TMPFLAGS')) {
	define('PR_ITEM_TMPFLAGS', 0x10970003);
}
if (!defined('PR_CONFLICT_ITEMS')) {
	define('PR_CONFLICT_ITEMS', 0x10981102);
}
if (!defined('PR_CDO_RECURRENCEID')) {
	define('PR_CDO_RECURRENCEID', 0x10C50040);
}
if (!defined('PR_ATTR_HIDDEN')) {
	define('PR_ATTR_HIDDEN', 0x10F4000B);
}
if (!defined('PR_ATTR_HIDDEN_GROMOX')) {
	define('PR_ATTR_HIDDEN_GROMOX', 0x10F40003);
}
if (!defined('PR_ATTR_SYSTEM')) {
	define('PR_ATTR_SYSTEM', 0x10F5000B);
}
if (!defined('PR_ATTR_READONLY')) {
	define('PR_ATTR_READONLY', 0x10F6000B);
}
if (!defined('PR_ROWID')) {
	define('PR_ROWID', 0x30000003);
}
if (!defined('PR_DISPLAY_NAME')) {
	define('PR_DISPLAY_NAME', 0x3001001E);
}
if (!defined('PR_DISPLAY_NAME_A')) {
	define('PR_DISPLAY_NAME_A', 0x3001001E);
}
if (!defined('PR_ADDRTYPE')) {
	define('PR_ADDRTYPE', 0x3002001E);
}
if (!defined('PR_ADDRTYPE_A')) {
	define('PR_ADDRTYPE_A', 0x3002001E);
}
if (!defined('PR_EMAIL_ADDRESS')) {
	define('PR_EMAIL_ADDRESS', 0x3003001E);
}
if (!defined('PR_EMAIL_ADDRESS_A')) {
	define('PR_EMAIL_ADDRESS_A', 0x3003001E);
}
if (!defined('PR_COMMENT')) {
	define('PR_COMMENT', 0x3004001E);
}
if (!defined('PR_DEPTH')) {
	define('PR_DEPTH', 0x30050003);
}
if (!defined('PR_PROVIDER_DISPLAY')) {
	define('PR_PROVIDER_DISPLAY', 0x3006001E);
}
if (!defined('PR_CREATION_TIME')) {
	define('PR_CREATION_TIME', 0x30070040);
}
if (!defined('PR_LAST_MODIFICATION_TIME')) {
	define('PR_LAST_MODIFICATION_TIME', 0x30080040);
}
if (!defined('PR_RESOURCE_FLAGS')) {
	define('PR_RESOURCE_FLAGS', 0x30090003);
}
if (!defined('PR_PROVIDER_DLL_NAME')) {
	define('PR_PROVIDER_DLL_NAME', 0x300A001E);
}
if (!defined('PR_SEARCH_KEY')) {
	define('PR_SEARCH_KEY', 0x300B0102);
}
if (!defined('PR_PROVIDER_UID')) {
	define('PR_PROVIDER_UID', 0x300C0102);
}
if (!defined('PR_PROVIDER_ORDINAL')) {
	define('PR_PROVIDER_ORDINAL', 0x300D0003);
}
if (!defined('PR_TARGET_ENTRYID')) {
	define('PR_TARGET_ENTRYID', 0x30100102);
}
if (!defined('PR_CONVERSATION_ID')) {
	define('PR_CONVERSATION_ID', 0x30130102);
}
if (!defined('PR_CONVERSATION_INDEX_TRACKING')) {
	define('PR_CONVERSATION_INDEX_TRACKING', 0x3016000B);
}
if (!defined('PR_ARCHIVE_TAG')) {
	define('PR_ARCHIVE_TAG', 0x30180102);
}
if (!defined('PR_POLICY_TAG')) {
	define('PR_POLICY_TAG', 0x30190102);
}
if (!defined('PR_RETENTION_PERIOD')) {
	define('PR_RETENTION_PERIOD', 0x301A0003);
}
if (!defined('PR_START_DATE_ETC')) {
	define('PR_START_DATE_ETC', 0x301B0102);
}
if (!defined('PR_RETENTION_DATE')) {
	define('PR_RETENTION_DATE', 0x301C0040);
}
if (!defined('PR_RETENTION_FLAGS')) {
	define('PR_RETENTION_FLAGS', 0x301D0003);
}
if (!defined('PR_ARCHIVE_PERIOD')) {
	define('PR_ARCHIVE_PERIOD', 0x301E0003);
}
if (!defined('PR_ARCHIVE_DATE')) {
	define('PR_ARCHIVE_DATE', 0x301F0040);
}
if (!defined('PR_SORT_POSITION')) {
	define('PR_SORT_POSITION', 0x30200102);
}
if (!defined('PR_SORT_PARENTID')) {
	define('PR_SORT_PARENTID', 0x30210102);
}
if (!defined('PR_FORM_VERSION')) {
	define('PR_FORM_VERSION', 0x3301001E);
}
if (!defined('PR_FORM_CLSID')) {
	define('PR_FORM_CLSID', 0x33020048);
}
if (!defined('PR_FORM_CONTACT_NAME')) {
	define('PR_FORM_CONTACT_NAME', 0x3303001E);
}
if (!defined('PR_FORM_CATEGORY')) {
	define('PR_FORM_CATEGORY', 0x3304001E);
}
if (!defined('PR_FORM_CATEGORY_SUB')) {
	define('PR_FORM_CATEGORY_SUB', 0x3305001E);
}
if (!defined('PR_FORM_HOST_MAP')) {
	define('PR_FORM_HOST_MAP', 0x33061003);
}
if (!defined('PR_FORM_HIDDEN')) {
	define('PR_FORM_HIDDEN', 0x3307000B);
}
if (!defined('PR_FORM_DESIGNER_NAME')) {
	define('PR_FORM_DESIGNER_NAME', 0x3308001E);
}
if (!defined('PR_FORM_DESIGNER_GUID')) {
	define('PR_FORM_DESIGNER_GUID', 0x33090048);
}
if (!defined('PR_FORM_MESSAGE_BEHAVIOR')) {
	define('PR_FORM_MESSAGE_BEHAVIOR', 0x330A0003);
}
if (!defined('PR_DEFAULT_STORE')) {
	define('PR_DEFAULT_STORE', 0x3400000B);
}
if (!defined('PR_STORE_SUPPORT_MASK')) {
	define('PR_STORE_SUPPORT_MASK', 0x340D0003);
}
if (!defined('PR_STORE_STATE')) {
	define('PR_STORE_STATE', 0x340E0003);
}
if (!defined('PR_STORE_UNICODE_MASK')) {
	define('PR_STORE_UNICODE_MASK', 0x340F0003);
}
if (!defined('PR_IPM_SUBTREE_SEARCH_KEY')) {
	define('PR_IPM_SUBTREE_SEARCH_KEY', 0x34100102);
}
if (!defined('PR_IPM_OUTBOX_SEARCH_KEY')) {
	define('PR_IPM_OUTBOX_SEARCH_KEY', 0x34110102);
}
if (!defined('PR_IPM_WASTEBASKET_SEARCH_KEY')) {
	define('PR_IPM_WASTEBASKET_SEARCH_KEY', 0x34120102);
}
if (!defined('PR_IPM_SENTMAIL_SEARCH_KEY')) {
	define('PR_IPM_SENTMAIL_SEARCH_KEY', 0x34130102);
}
if (!defined('PR_MDB_PROVIDER')) {
	define('PR_MDB_PROVIDER', 0x34140102);
}
if (!defined('PR_RECEIVE_FOLDER_SETTINGS')) {
	define('PR_RECEIVE_FOLDER_SETTINGS', 0x3415000D);
}
if (!defined('PR_QUOTA_WARNING')) {
	define('PR_QUOTA_WARNING', 0x34180003);
}
if (!defined('PR_QUOTA_SEND')) {
	define('PR_QUOTA_SEND', 0x34190003);
}
if (!defined('PR_QUOTA_RECEIVE')) {
	define('PR_QUOTA_RECEIVE', 0x341A0003);
}
if (!defined('PR_ROOT_ENTRYID')) {
	define('PR_ROOT_ENTRYID', 0x35D80102);
}
if (!defined('PR_VALID_FOLDER_MASK')) {
	define('PR_VALID_FOLDER_MASK', 0x35DF0003);
}
if (!defined('PR_IPM_SUBTREE_ENTRYID')) {
	define('PR_IPM_SUBTREE_ENTRYID', 0x35E00102);
}
if (!defined('PR_IPM_INBOX_ENTRYID')) {
	define('PR_IPM_INBOX_ENTRYID', 0x35E10102);
}
if (!defined('PR_IPM_OUTBOX_ENTRYID')) {
	define('PR_IPM_OUTBOX_ENTRYID', 0x35E20102);
}
if (!defined('PR_IPM_WASTEBASKET_ENTRYID')) {
	define('PR_IPM_WASTEBASKET_ENTRYID', 0x35E30102);
}
if (!defined('PR_IPM_SENTMAIL_ENTRYID')) {
	define('PR_IPM_SENTMAIL_ENTRYID', 0x35E40102);
}
if (!defined('PR_VIEWS_ENTRYID')) {
	define('PR_VIEWS_ENTRYID', 0x35E50102);
}
if (!defined('PR_COMMON_VIEWS_ENTRYID')) {
	define('PR_COMMON_VIEWS_ENTRYID', 0x35E60102);
}
if (!defined('PR_FINDER_ENTRYID')) {
	define('PR_FINDER_ENTRYID', 0x35E70102);
}
if (!defined('PR_SYNC_ROOT_ENTRYID')) {
	define('PR_SYNC_ROOT_ENTRYID', 0x35EA0102);
}
if (!defined('PR_VOICEMAIL_FOLDER_ENTRYID')) {
	define('PR_VOICEMAIL_FOLDER_ENTRYID', 0x35EB0102);
}
if (!defined('PR_CONTAINER_FLAGS')) {
	define('PR_CONTAINER_FLAGS', 0x36000003);
}
if (!defined('PR_FOLDER_TYPE')) {
	define('PR_FOLDER_TYPE', 0x36010003);
}
if (!defined('PR_CONTENT_COUNT')) {
	define('PR_CONTENT_COUNT', 0x36020003);
}
if (!defined('PR_CONTENT_UNREAD')) {
	define('PR_CONTENT_UNREAD', 0x36030003);
}
if (!defined('PR_CREATE_TEMPLATES')) {
	define('PR_CREATE_TEMPLATES', 0x3604000D);
}
if (!defined('PR_SEARCH')) {
	define('PR_SEARCH', 0x3607000D);
}
if (!defined('PR_SELECTABLE')) {
	define('PR_SELECTABLE', 0x3609000B);
}
if (!defined('PR_SUBFOLDERS')) {
	define('PR_SUBFOLDERS', 0x360A000B);
}
if (!defined('PR_STATUS')) {
	define('PR_STATUS', 0x360B0003);
}
if (!defined('PR_ANR')) {
	define('PR_ANR', 0x360C001E);
}
if (!defined('PR_ANR_A')) {
	define('PR_ANR_A', 0x360C001E);
}
if (!defined('PR_CONTENTS_SORT_ORDER')) {
	define('PR_CONTENTS_SORT_ORDER', 0x360D1003);
}
if (!defined('PR_CONTAINER_HIERARCHY')) {
	define('PR_CONTAINER_HIERARCHY', 0x360E000D);
}
if (!defined('PR_CONTAINER_CONTENTS')) {
	define('PR_CONTAINER_CONTENTS', 0x360F000D);
}
if (!defined('PR_FOLDER_ASSOCIATED_CONTENTS')) {
	define('PR_FOLDER_ASSOCIATED_CONTENTS', 0x3610000D);
}
if (!defined('PR_DEF_CREATE_DL')) {
	define('PR_DEF_CREATE_DL', 0x36110102);
}
if (!defined('PR_DEF_CREATE_MAILUSER')) {
	define('PR_DEF_CREATE_MAILUSER', 0x36120102);
}
if (!defined('PR_CONTAINER_CLASS')) {
	define('PR_CONTAINER_CLASS', 0x3613001E);
}
if (!defined('PR_CONTAINER_MODIFY_VERSION')) {
	define('PR_CONTAINER_MODIFY_VERSION', 0x36140014);
}
if (!defined('PR_AB_PROVIDER_ID')) {
	define('PR_AB_PROVIDER_ID', 0x36150102);
}
if (!defined('PR_DEFAULT_VIEW_ENTRYID')) {
	define('PR_DEFAULT_VIEW_ENTRYID', 0x36160102);
}
if (!defined('PR_ASSOC_CONTENT_COUNT')) {
	define('PR_ASSOC_CONTENT_COUNT', 0x36170003);
}
if (!defined('PR_IPM_APPOINTMENT_ENTRYID')) {
	define('PR_IPM_APPOINTMENT_ENTRYID', 0x36D00102);
}
if (!defined('PR_IPM_CONTACT_ENTRYID')) {
	define('PR_IPM_CONTACT_ENTRYID', 0x36D10102);
}
if (!defined('PR_IPM_JOURNAL_ENTRYID')) {
	define('PR_IPM_JOURNAL_ENTRYID', 0x36D20102);
}
if (!defined('PR_IPM_NOTE_ENTRYID')) {
	define('PR_IPM_NOTE_ENTRYID', 0x36D30102);
}
if (!defined('PR_IPM_TASK_ENTRYID')) {
	define('PR_IPM_TASK_ENTRYID', 0x36D40102);
}
if (!defined('PR_REM_ONLINE_ENTRYID')) {
	define('PR_REM_ONLINE_ENTRYID', 0x36D50102);
}
if (!defined('PR_REM_OFFLINE_ENTRYID')) {
	define('PR_REM_OFFLINE_ENTRYID', 0x36D60102);
}
if (!defined('PR_IPM_DRAFTS_ENTRYID')) {
	define('PR_IPM_DRAFTS_ENTRYID', 0x36D70102);
}
if (!defined('PR_ADDITIONAL_REN_ENTRYIDS')) {
	define('PR_ADDITIONAL_REN_ENTRYIDS', 0x36D81102);
}
if (!defined('PR_ADDITIONAL_REN_ENTRYIDS_EX')) {
	define('PR_ADDITIONAL_REN_ENTRYIDS_EX', 0x36D90102);
}
if (!defined('PR_EXTENDED_FOLDER_FLAGS')) {
	define('PR_EXTENDED_FOLDER_FLAGS', 0x36DA0102);
}
if (!defined('PR_NET_FOLDER_FLAGS')) {
	define('PR_NET_FOLDER_FLAGS', 0x36DE0003);
}
if (!defined('PR_FOLDER_WEBVIEWINFO')) {
	define('PR_FOLDER_WEBVIEWINFO', 0x36DF0102);
}
if (!defined('PR_FOLDER_XVIEWINFO_E')) {
	define('PR_FOLDER_XVIEWINFO_E', 0x36E00102);
}
if (!defined('PR_FOLDER_VIEWS_ONLY')) {
	define('PR_FOLDER_VIEWS_ONLY', 0x36E10003);
}
if (!defined('PR_ORDINAL_MOST')) {
	define('PR_ORDINAL_MOST', 0x36E20003);
}
if (!defined('PR_FREEBUSY_ENTRYIDS')) {
	define('PR_FREEBUSY_ENTRYIDS', 0x36E41102);
}
if (!defined('PR_DEF_POST_MSGCLASS')) {
	define('PR_DEF_POST_MSGCLASS', 0x36E5001E);
}
if (!defined('PR_DEF_POST_DISPLAYNAME')) {
	define('PR_DEF_POST_DISPLAYNAME', 0x36E6001E);
}
if (!defined('PR_GENERATE_EXCHANGE_VIEWS')) {
	define('PR_GENERATE_EXCHANGE_VIEWS', 0x36E9000B);
}
if (!defined('PR_ATTACHMENT_X400_PARAMETERS')) {
	define('PR_ATTACHMENT_X400_PARAMETERS', 0x37000102);
}
if (!defined('PR_ATTACH_DATA_BIN')) {
	define('PR_ATTACH_DATA_BIN', 0x37010102);
}
if (!defined('PR_ATTACH_DATA_OBJ')) {
	define('PR_ATTACH_DATA_OBJ', 0x3701000D);
}
if (!defined('PR_ATTACH_ENCODING')) {
	define('PR_ATTACH_ENCODING', 0x37020102);
}
if (!defined('PR_ATTACH_EXTENSION')) {
	define('PR_ATTACH_EXTENSION', 0x3703001E);
}
if (!defined('PR_ATTACH_EXTENSION_A')) {
	define('PR_ATTACH_EXTENSION_A', 0x3703001E);
}
if (!defined('PR_ATTACH_FILENAME')) {
	define('PR_ATTACH_FILENAME', 0x3704001E);
}
if (!defined('PR_ATTACH_FILENAME_A')) {
	define('PR_ATTACH_FILENAME_A', 0x3704001E);
}
if (!defined('PR_ATTACH_METHOD')) {
	define('PR_ATTACH_METHOD', 0x37050003);
}
if (!defined('PR_ATTACH_LONG_FILENAME')) {
	define('PR_ATTACH_LONG_FILENAME', 0x3707001E);
}
if (!defined('PR_ATTACH_LONG_FILENAME_A')) {
	define('PR_ATTACH_LONG_FILENAME_A', 0x3707001E);
}
if (!defined('PR_ATTACH_PATHNAME')) {
	define('PR_ATTACH_PATHNAME', 0x3708001E);
}
if (!defined('PR_ATTACH_RENDERING')) {
	define('PR_ATTACH_RENDERING', 0x37090102);
}
if (!defined('PR_ATTACH_TAG')) {
	define('PR_ATTACH_TAG', 0x370A0102);
}
if (!defined('PR_RENDERING_POSITION')) {
	define('PR_RENDERING_POSITION', 0x370B0003);
}
if (!defined('PR_ATTACH_TRANSPORT_NAME_A')) {
	define('PR_ATTACH_TRANSPORT_NAME_A', 0x370C001E);
}
if (!defined('PR_ATTACH_TRANSPORT_NAME')) {
	define('PR_ATTACH_TRANSPORT_NAME', 0x370C001E);
}
if (!defined('PR_ATTACH_LONG_PATHNAME')) {
	define('PR_ATTACH_LONG_PATHNAME', 0x370D001E);
}
if (!defined('PR_ATTACH_MIME_TAG')) {
	define('PR_ATTACH_MIME_TAG', 0x370E001E);
}
if (!defined('PR_ATTACH_ADDITIONAL_INFO')) {
	define('PR_ATTACH_ADDITIONAL_INFO', 0x370F0102);
}
if (!defined('PR_ATTACH_CONTENT_BASE')) {
	define('PR_ATTACH_CONTENT_BASE', 0x3711001E);
}
if (!defined('PR_ATTACH_CONTENT_BASE_A')) {
	define('PR_ATTACH_CONTENT_BASE_A', 0x3711001E);
}
if (!defined('PR_ATTACH_CONTENT_ID')) {
	define('PR_ATTACH_CONTENT_ID', 0x3712001E);
}
if (!defined('PR_ATTACH_CONTENT_ID_A')) {
	define('PR_ATTACH_CONTENT_ID_A', 0x3712001E);
}
if (!defined('PR_ATTACH_CONTENT_LOCATION')) {
	define('PR_ATTACH_CONTENT_LOCATION', 0x3713001E);
}
if (!defined('PR_ATTACH_CONTENT_LOCATION_A')) {
	define('PR_ATTACH_CONTENT_LOCATION_A', 0x3713001E);
}
if (!defined('PR_ATTACH_FLAGS')) {
	define('PR_ATTACH_FLAGS', 0x37140003);
}
if (!defined('PR_ATTACH_PAYLOAD_PROV_GUID_STR')) {
	define('PR_ATTACH_PAYLOAD_PROV_GUID_STR', 0x3719001E);
}
if (!defined('PR_ATTACH_PAYLOAD_CLASS')) {
	define('PR_ATTACH_PAYLOAD_CLASS', 0x371A001E);
}
if (!defined('PR_ATTACH_PAYLOAD_CLASS_A')) {
	define('PR_ATTACH_PAYLOAD_CLASS_A', 0x371A001E);
}
if (!defined('PR_DISPLAY_TYPE')) {
	define('PR_DISPLAY_TYPE', 0x39000003);
}
if (!defined('PR_TEMPLATEID')) {
	define('PR_TEMPLATEID', 0x39020102);
}
if (!defined('PR_PRIMARY_CAPABILITY')) {
	define('PR_PRIMARY_CAPABILITY', 0x39040102);
}
if (!defined('PR_DISPLAY_TYPE_EX')) {
	define('PR_DISPLAY_TYPE_EX', 0x39050003);
}
if (!defined('PR_SMTP_ADDRESS')) {
	define('PR_SMTP_ADDRESS', 0x39FE001E);
}
if (!defined('PR_SMTP_ADDRESS_A')) {
	define('PR_SMTP_ADDRESS_A', 0x39FE001E);
}
if (!defined('PR_EMS_AB_DISPLAY_NAME_PRINTABLE')) {
	define('PR_EMS_AB_DISPLAY_NAME_PRINTABLE', 0x39FF001E);
}
if (!defined('PR_EMS_AB_DISPLAY_NAME_PRINTABLE_A')) {
	define('PR_EMS_AB_DISPLAY_NAME_PRINTABLE_A', 0x39FF001E);
}
if (!defined('PR_7BIT_DISPLAY_NAME')) {
	define('PR_7BIT_DISPLAY_NAME', 0x39FF001E);
}
if (!defined('PR_ACCOUNT')) {
	define('PR_ACCOUNT', 0x3A00001E);
}
if (!defined('PR_ACCOUNT_A')) {
	define('PR_ACCOUNT_A', 0x3A00001E);
}
if (!defined('PR_ALTERNATE_RECIPIENT')) {
	define('PR_ALTERNATE_RECIPIENT', 0x3A010102);
}
if (!defined('PR_CALLBACK_TELEPHONE_NUMBER')) {
	define('PR_CALLBACK_TELEPHONE_NUMBER', 0x3A02001E);
}
if (!defined('PR_CONVERSION_PROHIBITED')) {
	define('PR_CONVERSION_PROHIBITED', 0x3A03000B);
}
if (!defined('PR_DISCLOSE_RECIPIENTS')) {
	define('PR_DISCLOSE_RECIPIENTS', 0x3A04000B);
}
if (!defined('PR_GENERATION')) {
	define('PR_GENERATION', 0x3A05001E);
}
if (!defined('PR_GIVEN_NAME')) {
	define('PR_GIVEN_NAME', 0x3A06001E);
}
if (!defined('PR_GOVERNMENT_ID_NUMBER')) {
	define('PR_GOVERNMENT_ID_NUMBER', 0x3A07001E);
}
if (!defined('PR_BUSINESS_TELEPHONE_NUMBER')) {
	define('PR_BUSINESS_TELEPHONE_NUMBER', 0x3A08001E);
}
if (!defined('PR_OFFICE_TELEPHONE_NUMBER')) {
	define('PR_OFFICE_TELEPHONE_NUMBER', 0x3A08001E);
}
if (!defined('PR_HOME_TELEPHONE_NUMBER')) {
	define('PR_HOME_TELEPHONE_NUMBER', 0x3A09001E);
}
if (!defined('PR_INITIALS')) {
	define('PR_INITIALS', 0x3A0A001E);
}
if (!defined('PR_KEYWORD')) {
	define('PR_KEYWORD', 0x3A0B001E);
}
if (!defined('PR_LANGUAGE')) {
	define('PR_LANGUAGE', 0x3A0C001E);
}
if (!defined('PR_LOCATION')) {
	define('PR_LOCATION', 0x3A0D001E);
}
if (!defined('PR_MAIL_PERMISSION')) {
	define('PR_MAIL_PERMISSION', 0x3A0E000B);
}
if (!defined('PR_MHS_COMMON_NAME')) {
	define('PR_MHS_COMMON_NAME', 0x3A0F001E);
}
if (!defined('PR_ORGANIZATIONAL_ID_NUMBER')) {
	define('PR_ORGANIZATIONAL_ID_NUMBER', 0x3A10001E);
}
if (!defined('PR_SURNAME')) {
	define('PR_SURNAME', 0x3A11001E);
}
if (!defined('PR_ORIGINAL_ENTRYID')) {
	define('PR_ORIGINAL_ENTRYID', 0x3A120102);
}
if (!defined('PR_ORIGINAL_DISPLAY_NAME')) {
	define('PR_ORIGINAL_DISPLAY_NAME', 0x3A13001E);
}
if (!defined('PR_ORIGINAL_SEARCH_KEY')) {
	define('PR_ORIGINAL_SEARCH_KEY', 0x3A140102);
}
if (!defined('PR_POSTAL_ADDRESS')) {
	define('PR_POSTAL_ADDRESS', 0x3A15001E);
}
if (!defined('PR_COMPANY_NAME')) {
	define('PR_COMPANY_NAME', 0x3A16001E);
}
if (!defined('PR_COMPANY_NAME_A')) {
	define('PR_COMPANY_NAME_A', 0x3A16001E);
}
if (!defined('PR_TITLE')) {
	define('PR_TITLE', 0x3A17001E);
}
if (!defined('PR_DEPARTMENT_NAME')) {
	define('PR_DEPARTMENT_NAME', 0x3A18001E);
}
if (!defined('PR_DEPARTMENT_NAME_A')) {
	define('PR_DEPARTMENT_NAME_A', 0x3A18001E);
}
if (!defined('PR_OFFICE_LOCATION')) {
	define('PR_OFFICE_LOCATION', 0x3A19001E);
}
if (!defined('PR_OFFICE_LOCATION_A')) {
	define('PR_OFFICE_LOCATION_A', 0x3A19001E);
}
if (!defined('PR_PRIMARY_TELEPHONE_NUMBER')) {
	define('PR_PRIMARY_TELEPHONE_NUMBER', 0x3A1A001E);
}
if (!defined('PR_PRIMARY_TELEPHONE_NUMBER_A')) {
	define('PR_PRIMARY_TELEPHONE_NUMBER_A', 0x3A1A001E);
}
if (!defined('PR_BUSINESS2_TELEPHONE_NUMBER')) {
	define('PR_BUSINESS2_TELEPHONE_NUMBER', 0x3A1B001E);
}
if (!defined('PR_BUSINESS2_TELEPHONE_NUMBER_MV')) {
	define('PR_BUSINESS2_TELEPHONE_NUMBER_MV', 0x3A1B101E);
}
if (!defined('PR_OFFICE2_TELEPHONE_NUMBER')) {
	define('PR_OFFICE2_TELEPHONE_NUMBER', 0x3A1B001E);
}
if (!defined('PR_MOBILE_TELEPHONE_NUMBER')) {
	define('PR_MOBILE_TELEPHONE_NUMBER', 0x3A1C001E);
}
if (!defined('PR_CELLULAR_TELEPHONE_NUMBER')) {
	define('PR_CELLULAR_TELEPHONE_NUMBER', 0x3A1C001E);
}
if (!defined('PR_RADIO_TELEPHONE_NUMBER')) {
	define('PR_RADIO_TELEPHONE_NUMBER', 0x3A1D001E);
}
if (!defined('PR_CAR_TELEPHONE_NUMBER')) {
	define('PR_CAR_TELEPHONE_NUMBER', 0x3A1E001E);
}
if (!defined('PR_OTHER_TELEPHONE_NUMBER')) {
	define('PR_OTHER_TELEPHONE_NUMBER', 0x3A1F001E);
}
if (!defined('PR_TRANSMITABLE_DISPLAY_NAME')) {
	define('PR_TRANSMITABLE_DISPLAY_NAME', 0x3A20001E);
}
if (!defined('PR_TRANSMITABLE_DISPLAY_NAME_A')) {
	define('PR_TRANSMITABLE_DISPLAY_NAME_A', 0x3A20001E);
}
if (!defined('PR_BEEPER_TELEPHONE_NUMBER')) {
	define('PR_BEEPER_TELEPHONE_NUMBER', 0x3A21001E);
}
if (!defined('PR_PAGER_TELEPHONE_NUMBER')) {
	define('PR_PAGER_TELEPHONE_NUMBER', 0x3A21001E);
}
if (!defined('PR_USER_CERTIFICATE')) {
	define('PR_USER_CERTIFICATE', 0x3A220102);
}
if (!defined('PR_PRIMARY_FAX_NUMBER')) {
	define('PR_PRIMARY_FAX_NUMBER', 0x3A23001E);
}
if (!defined('PR_BUSINESS_FAX_NUMBER')) {
	define('PR_BUSINESS_FAX_NUMBER', 0x3A24001E);
}
if (!defined('PR_HOME_FAX_NUMBER')) {
	define('PR_HOME_FAX_NUMBER', 0x3A25001E);
}
if (!defined('PR_COUNTRY')) {
	define('PR_COUNTRY', 0x3A26001E);
}
if (!defined('PR_BUSINESS_ADDRESS_COUNTRY')) {
	define('PR_BUSINESS_ADDRESS_COUNTRY', 0x3A26001E);
}
if (!defined('PR_LOCALITY')) {
	define('PR_LOCALITY', 0x3A27001E);
}
if (!defined('PR_BUSINESS_ADDRESS_CITY')) {
	define('PR_BUSINESS_ADDRESS_CITY', 0x3A27001E);
}
if (!defined('PR_STATE_OR_PROVINCE')) {
	define('PR_STATE_OR_PROVINCE', 0x3A28001E);
}
if (!defined('PR_BUSINESS_ADDRESS_STATE_OR_PROVINCE')) {
	define('PR_BUSINESS_ADDRESS_STATE_OR_PROVINCE', 0x3A28001E);
}
if (!defined('PR_STREET_ADDRESS')) {
	define('PR_STREET_ADDRESS', 0x3A29001E);
}
if (!defined('PR_BUSINESS_ADDRESS_STREET')) {
	define('PR_BUSINESS_ADDRESS_STREET', 0x3A29001E);
}
if (!defined('PR_POSTAL_CODE')) {
	define('PR_POSTAL_CODE', 0x3A2A001E);
}
if (!defined('PR_BUSINESS_ADDRESS_POSTAL_CODE')) {
	define('PR_BUSINESS_ADDRESS_POSTAL_CODE', 0x3A2A001E);
}
if (!defined('PR_POST_OFFICE_BOX')) {
	define('PR_POST_OFFICE_BOX', 0x3A2B001E);
}
if (!defined('PR_BUSINESS_ADDRESS_POST_OFFICE_BOX')) {
	define('PR_BUSINESS_ADDRESS_POST_OFFICE_BOX', 0x3A2B001E);
}
if (!defined('PR_TELEX_NUMBER')) {
	define('PR_TELEX_NUMBER', 0x3A2C001E);
}
if (!defined('PR_ISDN_NUMBER')) {
	define('PR_ISDN_NUMBER', 0x3A2D001E);
}
if (!defined('PR_ASSISTANT_TELEPHONE_NUMBER')) {
	define('PR_ASSISTANT_TELEPHONE_NUMBER', 0x3A2E001E);
}
if (!defined('PR_HOME2_TELEPHONE_NUMBER')) {
	define('PR_HOME2_TELEPHONE_NUMBER', 0x3A2F001E);
}
if (!defined('PR_HOME2_TELEPHONE_NUMBER_MV')) {
	define('PR_HOME2_TELEPHONE_NUMBER_MV', 0x3A2F101E);
}
if (!defined('PR_ASSISTANT')) {
	define('PR_ASSISTANT', 0x3A30001E);
}
if (!defined('PR_SEND_RICH_INFO')) {
	define('PR_SEND_RICH_INFO', 0x3A40000B);
}
if (!defined('PR_WEDDING_ANNIVERSARY')) {
	define('PR_WEDDING_ANNIVERSARY', 0x3A410040);
}
if (!defined('PR_BIRTHDAY')) {
	define('PR_BIRTHDAY', 0x3A420040);
}
if (!defined('PR_HOBBIES')) {
	define('PR_HOBBIES', 0x3A43001E);
}
if (!defined('PR_MIDDLE_NAME')) {
	define('PR_MIDDLE_NAME', 0x3A44001E);
}
if (!defined('PR_DISPLAY_NAME_PREFIX')) {
	define('PR_DISPLAY_NAME_PREFIX', 0x3A45001E);
}
if (!defined('PR_PROFESSION')) {
	define('PR_PROFESSION', 0x3A46001E);
}
if (!defined('PR_PREFERRED_BY_NAME')) {
	define('PR_PREFERRED_BY_NAME', 0x3A47001E);
}
if (!defined('PR_SPOUSE_NAME')) {
	define('PR_SPOUSE_NAME', 0x3A48001E);
}
if (!defined('PR_COMPUTER_NETWORK_NAME')) {
	define('PR_COMPUTER_NETWORK_NAME', 0x3A49001E);
}
if (!defined('PR_CUSTOMER_ID')) {
	define('PR_CUSTOMER_ID', 0x3A4A001E);
}
if (!defined('PR_TTYTDD_PHONE_NUMBER')) {
	define('PR_TTYTDD_PHONE_NUMBER', 0x3A4B001E);
}
if (!defined('PR_FTP_SITE')) {
	define('PR_FTP_SITE', 0x3A4C001E);
}
if (!defined('PR_GENDER')) {
	define('PR_GENDER', 0x3A4D0002);
}
if (!defined('PR_MANAGER_NAME')) {
	define('PR_MANAGER_NAME', 0x3A4E001E);
}
if (!defined('PR_NICKNAME')) {
	define('PR_NICKNAME', 0x3A4F001E);
}
if (!defined('PR_PERSONAL_HOME_PAGE')) {
	define('PR_PERSONAL_HOME_PAGE', 0x3A50001E);
}
if (!defined('PR_BUSINESS_HOME_PAGE')) {
	define('PR_BUSINESS_HOME_PAGE', 0x3A51001E);
}
if (!defined('PR_CONTACT_VERSION')) {
	define('PR_CONTACT_VERSION', 0x3A520048);
}
if (!defined('PR_CONTACT_ENTRYIDS')) {
	define('PR_CONTACT_ENTRYIDS', 0x3A531102);
}
if (!defined('PR_CONTACT_ADDRTYPES')) {
	define('PR_CONTACT_ADDRTYPES', 0x3A54101E);
}
if (!defined('PR_CONTACT_DEFAULT_ADDRESS_INDEX')) {
	define('PR_CONTACT_DEFAULT_ADDRESS_INDEX', 0x3A550003);
}
if (!defined('PR_CONTACT_EMAIL_ADDRESSES')) {
	define('PR_CONTACT_EMAIL_ADDRESSES', 0x3A56101E);
}
if (!defined('PR_COMPANY_MAIN_PHONE_NUMBER')) {
	define('PR_COMPANY_MAIN_PHONE_NUMBER', 0x3A57001E);
}
if (!defined('PR_CHILDRENS_NAMES')) {
	define('PR_CHILDRENS_NAMES', 0x3A58101E);
}
if (!defined('PR_HOME_ADDRESS_CITY')) {
	define('PR_HOME_ADDRESS_CITY', 0x3A59001E);
}
if (!defined('PR_HOME_ADDRESS_COUNTRY')) {
	define('PR_HOME_ADDRESS_COUNTRY', 0x3A5A001E);
}
if (!defined('PR_HOME_ADDRESS_POSTAL_CODE')) {
	define('PR_HOME_ADDRESS_POSTAL_CODE', 0x3A5B001E);
}
if (!defined('PR_HOME_ADDRESS_STATE_OR_PROVINCE')) {
	define('PR_HOME_ADDRESS_STATE_OR_PROVINCE', 0x3A5C001E);
}
if (!defined('PR_HOME_ADDRESS_STREET')) {
	define('PR_HOME_ADDRESS_STREET', 0x3A5D001E);
}
if (!defined('PR_HOME_ADDRESS_POST_OFFICE_BOX')) {
	define('PR_HOME_ADDRESS_POST_OFFICE_BOX', 0x3A5E001E);
}
if (!defined('PR_OTHER_ADDRESS_CITY')) {
	define('PR_OTHER_ADDRESS_CITY', 0x3A5F001E);
}
if (!defined('PR_OTHER_ADDRESS_COUNTRY')) {
	define('PR_OTHER_ADDRESS_COUNTRY', 0x3A60001E);
}
if (!defined('PR_OTHER_ADDRESS_POSTAL_CODE')) {
	define('PR_OTHER_ADDRESS_POSTAL_CODE', 0x3A61001E);
}
if (!defined('PR_OTHER_ADDRESS_STATE_OR_PROVINCE')) {
	define('PR_OTHER_ADDRESS_STATE_OR_PROVINCE', 0x3A62001E);
}
if (!defined('PR_OTHER_ADDRESS_STREET')) {
	define('PR_OTHER_ADDRESS_STREET', 0x3A63001E);
}
if (!defined('PR_OTHER_ADDRESS_POST_OFFICE_BOX')) {
	define('PR_OTHER_ADDRESS_POST_OFFICE_BOX', 0x3A64001E);
}
if (!defined('PR_USER_X509_CERTIFICATE')) {
	define('PR_USER_X509_CERTIFICATE', 0x3A701102);
}
if (!defined('PR_SEND_INTERNET_ENCODING')) {
	define('PR_SEND_INTERNET_ENCODING', 0x3A710003);
}
if (!defined('PR_STORE_PROVIDERS')) {
	define('PR_STORE_PROVIDERS', 0x3D000102);
}
if (!defined('PR_AB_PROVIDERS')) {
	define('PR_AB_PROVIDERS', 0x3D010102);
}
if (!defined('PR_TRANSPORT_PROVIDERS')) {
	define('PR_TRANSPORT_PROVIDERS', 0x3D020102);
}
if (!defined('PR_DEFAULT_PROFILE')) {
	define('PR_DEFAULT_PROFILE', 0x3D04000B);
}
if (!defined('PR_AB_SEARCH_PATH')) {
	define('PR_AB_SEARCH_PATH', 0x3D051102);
}
if (!defined('PR_AB_DEFAULT_DIR')) {
	define('PR_AB_DEFAULT_DIR', 0x3D060102);
}
if (!defined('PR_AB_DEFAULT_PAB')) {
	define('PR_AB_DEFAULT_PAB', 0x3D070102);
}
if (!defined('PR_FILTERING_HOOKS')) {
	define('PR_FILTERING_HOOKS', 0x3D080102);
}
if (!defined('PR_SERVICE_NAME')) {
	define('PR_SERVICE_NAME', 0x3D09001E);
}
if (!defined('PR_SERVICE_DLL_NAME')) {
	define('PR_SERVICE_DLL_NAME', 0x3D0A001E);
}
if (!defined('PR_SERVICE_ENTRY_NAME')) {
	define('PR_SERVICE_ENTRY_NAME', 0x3D0B001E);
}
if (!defined('PR_SERVICE_UID')) {
	define('PR_SERVICE_UID', 0x3D0C0102);
}
if (!defined('PR_SERVICE_EXTRA_UIDS')) {
	define('PR_SERVICE_EXTRA_UIDS', 0x3D0D0102);
}
if (!defined('PR_SERVICES')) {
	define('PR_SERVICES', 0x3D0E0102);
}
if (!defined('PR_SERVICE_SUPPORT_FILES')) {
	define('PR_SERVICE_SUPPORT_FILES', 0x3D0F101E);
}
if (!defined('PR_SERVICE_DELETE_FILES')) {
	define('PR_SERVICE_DELETE_FILES', 0x3D10101E);
}
if (!defined('PR_AB_SEARCH_PATH_UPDATE')) {
	define('PR_AB_SEARCH_PATH_UPDATE', 0x3D110102);
}
if (!defined('PR_PROFILE_NAME')) {
	define('PR_PROFILE_NAME', 0x3D12001E);
}
if (!defined('PR_EMSMDB_SECTION_UID')) {
	define('PR_EMSMDB_SECTION_UID', 0x3D150102);
}
if (!defined('PR_EMSMDB_LEGACY')) {
	define('PR_EMSMDB_LEGACY', 0x3D18000B);
}
if (!defined('PR_EMSABP_USER_UID')) {
	define('PR_EMSABP_USER_UID', 0x3D1A0102);
}
if (!defined('PR_AB_CHOOSE_DIRECTORY_AUTOMATICALLY')) {
	define('PR_AB_CHOOSE_DIRECTORY_AUTOMATICALLY', 0x3D1C000B);
}
if (!defined('PR_CORRELATION_ID')) {
	define('PR_CORRELATION_ID', 0x3DD10048);
}
if (!defined('PR_IDENTITY_DISPLAY')) {
	define('PR_IDENTITY_DISPLAY', 0x3E00001E);
}
if (!defined('PR_IDENTITY_ENTRYID')) {
	define('PR_IDENTITY_ENTRYID', 0x3E010102);
}
if (!defined('PR_RESOURCE_METHODS')) {
	define('PR_RESOURCE_METHODS', 0x3E020003);
}
if (!defined('PR_RESOURCE_TYPE')) {
	define('PR_RESOURCE_TYPE', 0x3E030003);
}
if (!defined('PR_STATUS_CODE')) {
	define('PR_STATUS_CODE', 0x3E040003);
}
if (!defined('PR_IDENTITY_SEARCH_KEY')) {
	define('PR_IDENTITY_SEARCH_KEY', 0x3E050102);
}
if (!defined('PR_OWN_STORE_ENTRYID')) {
	define('PR_OWN_STORE_ENTRYID', 0x3E060102);
}
if (!defined('PR_RESOURCE_PATH')) {
	define('PR_RESOURCE_PATH', 0x3E07001E);
}
if (!defined('PR_STATUS_STRING')) {
	define('PR_STATUS_STRING', 0x3E08001E);
}
if (!defined('PR_X400_DEFERRED_DELIVERY_CANCEL')) {
	define('PR_X400_DEFERRED_DELIVERY_CANCEL', 0x3E09000B);
}
if (!defined('PR_HEADER_FOLDER_ENTRYID')) {
	define('PR_HEADER_FOLDER_ENTRYID', 0x3E0A0102);
}
if (!defined('PR_REMOTE_PROGRESS')) {
	define('PR_REMOTE_PROGRESS', 0x3E0B0003);
}
if (!defined('PR_REMOTE_PROGRESS_TEXT')) {
	define('PR_REMOTE_PROGRESS_TEXT', 0x3E0C001E);
}
if (!defined('PR_REMOTE_VALIDATE_OK')) {
	define('PR_REMOTE_VALIDATE_OK', 0x3E0D000B);
}
if (!defined('PR_CONTROL_FLAGS')) {
	define('PR_CONTROL_FLAGS', 0x3F000003);
}
if (!defined('PR_CONTROL_STRUCTURE')) {
	define('PR_CONTROL_STRUCTURE', 0x3F010102);
}
if (!defined('PR_CONTROL_TYPE')) {
	define('PR_CONTROL_TYPE', 0x3F020003);
}
if (!defined('PR_DELTAX')) {
	define('PR_DELTAX', 0x3F030003);
}
if (!defined('PR_DELTAY')) {
	define('PR_DELTAY', 0x3F040003);
}
if (!defined('PR_XPOS')) {
	define('PR_XPOS', 0x3F050003);
}
if (!defined('PR_YPOS')) {
	define('PR_YPOS', 0x3F060003);
}
if (!defined('PR_CONTROL_ID')) {
	define('PR_CONTROL_ID', 0x3F070102);
}
if (!defined('PR_INITIAL_DETAILS_PANE')) {
	define('PR_INITIAL_DETAILS_PANE', 0x3F080003);
}
if (!defined('PR_PREVIEW_UNREAD')) {
	define('PR_PREVIEW_UNREAD', 0x3FD8001E);
}
if (!defined('PR_PREVIEW')) {
	define('PR_PREVIEW', 0x3FD9001E);
}
if (!defined('PR_INTERNET_CPID')) {
	define('PR_INTERNET_CPID', 0x3FDE0003);
}
if (!defined('PR_AUTO_RESPONSE_SUPPRESS')) {
	define('PR_AUTO_RESPONSE_SUPPRESS', 0x3FDF0003);
}
if (!defined('PR_ACL_DATA')) {
	define('PR_ACL_DATA', 0x3FE00102);
}
if (!defined('PR_ACL_TABLE')) {
	define('PR_ACL_TABLE', 0x3FE0000D);
}
if (!defined('PR_RULES_DATA')) {
	define('PR_RULES_DATA', 0x3FE10102);
}
if (!defined('PR_RULES_TABLE')) {
	define('PR_RULES_TABLE', 0x3FE1000D);
}
if (!defined('PR_DELEGATED_BY_RULE')) {
	define('PR_DELEGATED_BY_RULE', 0x3FE3000B);
}
if (!defined('PR_RESOLVE_METHOD')) {
	define('PR_RESOLVE_METHOD', 0x3FE70003);
}
if (!defined('PR_HAS_DAMS')) {
	define('PR_HAS_DAMS', 0x3FEA000B);
}
if (!defined('PR_DEFERRED_SEND_NUMBER')) {
	define('PR_DEFERRED_SEND_NUMBER', 0x3FEB0003);
}
if (!defined('PR_DEFERRED_SEND_UNITS')) {
	define('PR_DEFERRED_SEND_UNITS', 0x3FEC0003);
}
if (!defined('PR_EXPIRY_NUMBER')) {
	define('PR_EXPIRY_NUMBER', 0x3FED0003);
}
if (!defined('PR_EXPIRY_UNITS')) {
	define('PR_EXPIRY_UNITS', 0x3FEE0003);
}
if (!defined('PR_DEFERRED_SEND_TIME')) {
	define('PR_DEFERRED_SEND_TIME', 0x3FEF0040);
}
if (!defined('PR_CONFLICT_ENTRYID')) {
	define('PR_CONFLICT_ENTRYID', 0x3FF00102);
}
if (!defined('PR_MESSAGE_LOCALE_ID')) {
	define('PR_MESSAGE_LOCALE_ID', 0x3FF10003);
}
if (!defined('PR_STORAGE_QUOTA_LIMIT')) {
	define('PR_STORAGE_QUOTA_LIMIT', 0x3FF50003);
}
if (!defined('PR_CREATOR_NAME')) {
	define('PR_CREATOR_NAME', 0x3FF8001E);
}
if (!defined('PR_CREATOR_ENTRYID')) {
	define('PR_CREATOR_ENTRYID', 0x3FF90102);
}
if (!defined('PR_LAST_MODIFIER_NAME')) {
	define('PR_LAST_MODIFIER_NAME', 0x3FFA001E);
}
if (!defined('PR_LAST_MODIFIER_ENTRYID')) {
	define('PR_LAST_MODIFIER_ENTRYID', 0x3FFB0102);
}
if (!defined('PR_MESSAGE_CODEPAGE')) {
	define('PR_MESSAGE_CODEPAGE', 0x3FFD0003);
}
if (!defined('PR_SENT_REPRESENTING_FLAGS')) {
	define('PR_SENT_REPRESENTING_FLAGS', 0x401A0003);
}
if (!defined('PR_CONTENT_FILTER_SCL')) {
	define('PR_CONTENT_FILTER_SCL', 0x40760003);
}
if (!defined('PR_SENDER_ID_STATUS')) {
	define('PR_SENDER_ID_STATUS', 0x40790003);
}
if (!defined('PR_HIER_REV')) {
	define('PR_HIER_REV', 0x40820040);
}
if (!defined('PR_PURPORTED_SENDER_DOMAIN')) {
	define('PR_PURPORTED_SENDER_DOMAIN', 0x4083001E);
}
if (!defined('PR_PURPORTED_SENDER_DOMAIN_A')) {
	define('PR_PURPORTED_SENDER_DOMAIN_A', 0x4083001E);
}
if (!defined('PR_INETMAIL_OVERRIDE_FORMAT')) {
	define('PR_INETMAIL_OVERRIDE_FORMAT', 0x59020003);
}
if (!defined('PR_MSG_EDITOR_FORMAT')) {
	define('PR_MSG_EDITOR_FORMAT', 0x59090003);
}
if (!defined('PR_SENDER_SMTP_ADDRESS')) {
	define('PR_SENDER_SMTP_ADDRESS', 0x5D01001E);
}
if (!defined('PR_SENT_REPRESENTING_SMTP_ADDRESS')) {
	define('PR_SENT_REPRESENTING_SMTP_ADDRESS', 0x5D02001E);
}
if (!defined('PR_RECIPIENT_ORDER')) {
	define('PR_RECIPIENT_ORDER', 0x5FDF0003);
}
if (!defined('PR_RECIPIENT_PROPOSED')) {
	define('PR_RECIPIENT_PROPOSED', 0x5FE1000B);
}
if (!defined('PR_RECIPIENT_PROPOSEDSTARTTIME')) {
	define('PR_RECIPIENT_PROPOSEDSTARTTIME', 0x5FE30040);
}
if (!defined('PR_RECIPIENT_PROPOSEDENDTIME')) {
	define('PR_RECIPIENT_PROPOSEDENDTIME', 0x5FE40040);
}
if (!defined('PR_RECIPIENT_DISPLAY_NAME')) {
	define('PR_RECIPIENT_DISPLAY_NAME', 0x5FF6001E);
}
if (!defined('PR_RECIPIENT_ENTRYID')) {
	define('PR_RECIPIENT_ENTRYID', 0x5FF70102);
}
if (!defined('PR_RECIPIENT_TRACKSTATUS_TIME')) {
	define('PR_RECIPIENT_TRACKSTATUS_TIME', 0x5FFB0040);
}
if (!defined('PR_RECIPIENT_FLAGS')) {
	define('PR_RECIPIENT_FLAGS', 0x5FFD0003);
}
if (!defined('PR_RECIPIENT_TRACKSTATUS')) {
	define('PR_RECIPIENT_TRACKSTATUS', 0x5FFF0003);
}
if (!defined('PR_JUNK_INCLUDE_CONTACTS')) {
	define('PR_JUNK_INCLUDE_CONTACTS', 0x61000003);
}
if (!defined('PR_JUNK_THRESHOLD')) {
	define('PR_JUNK_THRESHOLD', 0x61010003);
}
if (!defined('PR_JUNK_PERMANENTLY_DELETE')) {
	define('PR_JUNK_PERMANENTLY_DELETE', 0x61020003);
}
if (!defined('PR_JUNK_ADD_RECIPS_TO_SSL')) {
	define('PR_JUNK_ADD_RECIPS_TO_SSL', 0x61030003);
}
if (!defined('PR_JUNK_PHISHING_ENABLE_LINKS')) {
	define('PR_JUNK_PHISHING_ENABLE_LINKS', 0x6107000B);
}
if (!defined('PR_REPLY_TEMPLATE_ID')) {
	define('PR_REPLY_TEMPLATE_ID', 0x65C20102);
}
if (!defined('PR_SECURE_SUBMIT_FLAGS')) {
	define('PR_SECURE_SUBMIT_FLAGS', 0x65C60003);
}
if (!defined('PR_SOURCE_KEY')) {
	define('PR_SOURCE_KEY', 0x65E00102);
}
if (!defined('PR_PARENT_SOURCE_KEY')) {
	define('PR_PARENT_SOURCE_KEY', 0x65E10102);
}
if (!defined('PR_CHANGE_KEY')) {
	define('PR_CHANGE_KEY', 0x65E20102);
}
if (!defined('PR_PREDECESSOR_CHANGE_LIST')) {
	define('PR_PREDECESSOR_CHANGE_LIST', 0x65E30102);
}
if (!defined('PR_RULE_MSG_STATE')) {
	define('PR_RULE_MSG_STATE', 0x65E90003);
}
if (!defined('PR_RULE_MSG_USER_FLAGS')) {
	define('PR_RULE_MSG_USER_FLAGS', 0x65EA0003);
}
if (!defined('PR_RULE_MSG_PROVIDER')) {
	define('PR_RULE_MSG_PROVIDER', 0x65EB001E);
}
if (!defined('PR_RULE_MSG_NAME')) {
	define('PR_RULE_MSG_NAME', 0x65EC001E);
}
if (!defined('PR_RULE_MSG_LEVEL')) {
	define('PR_RULE_MSG_LEVEL', 0x65ED0003);
}
if (!defined('PR_RULE_MSG_PROVIDER_DATA')) {
	define('PR_RULE_MSG_PROVIDER_DATA', 0x65EE0102);
}
if (!defined('PR_RULE_MSG_SEQUENCE')) {
	define('PR_RULE_MSG_SEQUENCE', 0x65F30003);
}
if (!defined('PR_PST_RECEIVE_FOLDER_NID')) {
	define('PR_PST_RECEIVE_FOLDER_NID', 0x66050003);
}
if (!defined('PR_PROFILE_TRANSPORT_FLAGS')) {
	define('PR_PROFILE_TRANSPORT_FLAGS', 0x66050003);
}
if (!defined('PR_USER_ENTRYID')) {
	define('PR_USER_ENTRYID', 0x66190102);
}
if (!defined('PR_USER_NAME')) {
	define('PR_USER_NAME', 0x661A001E);
}
if (!defined('PR_MAILBOX_OWNER_ENTRYID')) {
	define('PR_MAILBOX_OWNER_ENTRYID', 0x661B0102);
}
if (!defined('PR_MAILBOX_OWNER_NAME')) {
	define('PR_MAILBOX_OWNER_NAME', 0x661C001E);
}
if (!defined('PR_MAILBOX_OWNER_NAME_A')) {
	define('PR_MAILBOX_OWNER_NAME_A', 0x661C001E);
}
if (!defined('PR_OOF_STATE')) {
	define('PR_OOF_STATE', 0x661D000B);
}
if (!defined('PR_SCHEDULE_FOLDER_ENTRYID')) {
	define('PR_SCHEDULE_FOLDER_ENTRYID', 0x661E0102);
}
if (!defined('PR_IPM_DAF_ENTRYID')) {
	define('PR_IPM_DAF_ENTRYID', 0x661F0102);
}
if (!defined('PR_NON_IPM_SUBTREE_ENTRYID')) {
	define('PR_NON_IPM_SUBTREE_ENTRYID', 0x66200102);
}
if (!defined('PR_EFORMS_REGISTRY_ENTRYID')) {
	define('PR_EFORMS_REGISTRY_ENTRYID', 0x66210102);
}
if (!defined('PR_SPLUS_FREE_BUSY_ENTRYID')) {
	define('PR_SPLUS_FREE_BUSY_ENTRYID', 0x66220102);
}
if (!defined('PR_OFFLINE_ADDRBOOK_ENTRYID')) {
	define('PR_OFFLINE_ADDRBOOK_ENTRYID', 0x66230102);
}
if (!defined('PR_TEST_LINE_SPEED')) {
	define('PR_TEST_LINE_SPEED', 0x662B0102);
}
if (!defined('PR_HIERARCHY_SYNCHRONIZER')) {
	define('PR_HIERARCHY_SYNCHRONIZER', 0x662C000D);
}
if (!defined('PR_CONTENTS_SYNCHRONIZER')) {
	define('PR_CONTENTS_SYNCHRONIZER', 0x662D000D);
}
if (!defined('PR_COLLECTOR')) {
	define('PR_COLLECTOR', 0x662E000D);
}
if (!defined('PR_IPM_FAVORITES_ENTRYID')) {
	define('PR_IPM_FAVORITES_ENTRYID', 0x66300102);
}
if (!defined('PR_IPM_PUBLIC_FOLDERS_ENTRYID')) {
	define('PR_IPM_PUBLIC_FOLDERS_ENTRYID', 0x66310102);
}
if (!defined('PR_STORE_OFFLINE')) {
	define('PR_STORE_OFFLINE', 0x6632000B);
}
if (!defined('PR_HIERARCHY_SERVER')) {
	define('PR_HIERARCHY_SERVER', 0x6633001E);
}
if (!defined('PR_PST_LRNORESTRICTIONS')) {
	define('PR_PST_LRNORESTRICTIONS', 0x6633000B);
}
if (!defined('PR_FAVORITES_DEFAULT_NAME')) {
	define('PR_FAVORITES_DEFAULT_NAME', 0x6635001E);
}
if (!defined('PR_PROFILE_OAB_COUNT_ATTEMPTED_FULLDN')) {
	define('PR_PROFILE_OAB_COUNT_ATTEMPTED_FULLDN', 0x66350003);
}
if (!defined('PR_PST_HIDDEN_COUNT')) {
	define('PR_PST_HIDDEN_COUNT', 0x66350003);
}
if (!defined('PR_PROFILE_OAB_COUNT_ATTEMPTED_INCRDN')) {
	define('PR_PROFILE_OAB_COUNT_ATTEMPTED_INCRDN', 0x66360003);
}
if (!defined('PR_PST_HIDDEN_UNREAD')) {
	define('PR_PST_HIDDEN_UNREAD', 0x66360003);
}
if (!defined('PR_FOLDER_CHILD_COUNT')) {
	define('PR_FOLDER_CHILD_COUNT', 0x66380003);
}
if (!defined('PR_RIGHTS')) {
	define('PR_RIGHTS', 0x66390003);
}
if (!defined('PR_HAS_RULES')) {
	define('PR_HAS_RULES', 0x663A000B);
}
if (!defined('PR_ADDRESS_BOOK_ENTRYID')) {
	define('PR_ADDRESS_BOOK_ENTRYID', 0x663B0102);
}
if (!defined('PR_HIERARCHY_CHANGE_NUM')) {
	define('PR_HIERARCHY_CHANGE_NUM', 0x663E0003);
}
if (!defined('PR_DELETED_MSG_COUNT')) {
	define('PR_DELETED_MSG_COUNT', 0x66400003);
}
if (!defined('PR_DELETED_FOLDER_COUNT')) {
	define('PR_DELETED_FOLDER_COUNT', 0x66410003);
}
if (!defined('PR_DELETED_ASSOC_MSG_COUNT')) {
	define('PR_DELETED_ASSOC_MSG_COUNT', 0x66430003);
}
if (!defined('PR_REPLICA_SERVER')) {
	define('PR_REPLICA_SERVER', 0x6644001E);
}
if (!defined('PR_CLIENT_ACTIONS')) {
	define('PR_CLIENT_ACTIONS', 0x66450102);
}
if (!defined('PR_DAM_ORIGINAL_ENTRYID')) {
	define('PR_DAM_ORIGINAL_ENTRYID', 0x66460102);
}
if (!defined('PR_DAM_BACK_PATCHED')) {
	define('PR_DAM_BACK_PATCHED', 0x6647000B);
}
if (!defined('PR_RULE_ERROR')) {
	define('PR_RULE_ERROR', 0x66480003);
}
if (!defined('PR_RULE_ACTION_TYPE')) {
	define('PR_RULE_ACTION_TYPE', 0x66490003);
}
if (!defined('PR_HAS_NAMED_PROPERTIES')) {
	define('PR_HAS_NAMED_PROPERTIES', 0x664A000B);
}
if (!defined('PR_RULE_ACTION_NUMBER')) {
	define('PR_RULE_ACTION_NUMBER', 0x66500003);
}
if (!defined('PR_RULE_FOLDER_ENTRYID')) {
	define('PR_RULE_FOLDER_ENTRYID', 0x66510102);
}
if (!defined('PR_PROHIBIT_RECEIVE_QUOTA')) {
	define('PR_PROHIBIT_RECEIVE_QUOTA', 0x666A0003);
}
if (!defined('PR_IN_CONFLICT')) {
	define('PR_IN_CONFLICT', 0x666C000B);
}
if (!defined('PR_MAX_SUBMIT_MESSAGE_SIZE')) {
	define('PR_MAX_SUBMIT_MESSAGE_SIZE', 0x666D0003);
}
if (!defined('PR_PROHIBIT_SEND_QUOTA')) {
	define('PR_PROHIBIT_SEND_QUOTA', 0x666E0003);
}
if (!defined('PR_EC_USER_LANGUAGE')) {
	define('PR_EC_USER_LANGUAGE', 0x6670001E);
}
if (!defined('PR_EC_USER_TIMEZONE')) {
	define('PR_EC_USER_TIMEZONE', 0x6671001E);
}
if (!defined('PR_MEMBER_ID')) {
	define('PR_MEMBER_ID', 0x66710014);
}
if (!defined('PR_MEMBER_NAME')) {
	define('PR_MEMBER_NAME', 0x6672001E);
}
if (!defined('PR_MEMBER_NAME_A')) {
	define('PR_MEMBER_NAME_A', 0x6672001E);
}
if (!defined('PR_MEMBER_RIGHTS')) {
	define('PR_MEMBER_RIGHTS', 0x66730003);
}
if (!defined('PR_RULE_ID')) {
	define('PR_RULE_ID', 0x66740014);
}
if (!defined('PR_RULE_IDS')) {
	define('PR_RULE_IDS', 0x66750102);
}
if (!defined('PR_RULE_SEQUENCE')) {
	define('PR_RULE_SEQUENCE', 0x66760003);
}
if (!defined('PR_RULE_STATE')) {
	define('PR_RULE_STATE', 0x66770003);
}
if (!defined('PR_RULE_USER_FLAGS')) {
	define('PR_RULE_USER_FLAGS', 0x66780003);
}
if (!defined('PR_RULE_CONDITION')) {
	define('PR_RULE_CONDITION', 0x667900FD);
}
if (!defined('PR_RULE_ACTIONS')) {
	define('PR_RULE_ACTIONS', 0x668000FE);
}
if (!defined('PR_RULE_PROVIDER')) {
	define('PR_RULE_PROVIDER', 0x6681001E);
}
if (!defined('PR_RULE_PROVIDER_A')) {
	define('PR_RULE_PROVIDER_A', 0x6681001E);
}
if (!defined('PR_RULE_NAME')) {
	define('PR_RULE_NAME', 0x6682001E);
}
if (!defined('PR_RULE_NAME_A')) {
	define('PR_RULE_NAME_A', 0x6682001E);
}
if (!defined('PR_RULE_LEVEL')) {
	define('PR_RULE_LEVEL', 0x66830003);
}
if (!defined('PR_RULE_PROVIDER_DATA')) {
	define('PR_RULE_PROVIDER_DATA', 0x66840102);
}
if (!defined('PR_RULE_VERSION')) {
	define('PR_RULE_VERSION', 0x668D0002);
}
if (!defined('PR_DELETED_ON')) {
	define('PR_DELETED_ON', 0x668F0040);
}
if (!defined('PR_DELETED_MESSAGE_SIZE')) {
	define('PR_DELETED_MESSAGE_SIZE', 0x669B0003);
}
if (!defined('PR_DELETED_MESSAGE_SIZE_EXTENDED')) {
	define('PR_DELETED_MESSAGE_SIZE_EXTENDED', 0x669B0014);
}
if (!defined('PR_DELETED_NORMAL_MESSAGE_SIZE')) {
	define('PR_DELETED_NORMAL_MESSAGE_SIZE', 0x669C0003);
}
if (!defined('PR_DELETED_NORMAL_MESSAGE_SIZE_EXTENDED')) {
	define('PR_DELETED_NORMAL_MESSAGE_SIZE_EXTENDED', 0x669C0014);
}
if (!defined('PR_DELETED_ASSOC_MESSAGE_SIZE')) {
	define('PR_DELETED_ASSOC_MESSAGE_SIZE', 0x669D0003);
}
if (!defined('PR_DELETED_ASSOC_MESSAGE_SIZE_EXTENDED')) {
	define('PR_DELETED_ASSOC_MESSAGE_SIZE_EXTENDED', 0x669D0014);
}
if (!defined('PR_LOCALE_ID')) {
	define('PR_LOCALE_ID', 0x66A10003);
}
if (!defined('PR_FOLDER_FLAGS')) {
	define('PR_FOLDER_FLAGS', 0x66A80003);
}
if (!defined('PR_NORMAL_MSG_W_ATTACH_COUNT')) {
	define('PR_NORMAL_MSG_W_ATTACH_COUNT', 0x66AD0003);
}
if (!defined('PR_ASSOC_MSG_W_ATTACH_COUNT')) {
	define('PR_ASSOC_MSG_W_ATTACH_COUNT', 0x66AE0003);
}
if (!defined('PR_NORMAL_MESSAGE_SIZE')) {
	define('PR_NORMAL_MESSAGE_SIZE', 0x66B30003);
}
if (!defined('PR_NORMAL_MESSAGE_SIZE_EXTENDED')) {
	define('PR_NORMAL_MESSAGE_SIZE_EXTENDED', 0x66B30014);
}
if (!defined('PR_ASSOC_MESSAGE_SIZE')) {
	define('PR_ASSOC_MESSAGE_SIZE', 0x66B40003);
}
if (!defined('PR_ASSOC_MESSAGE_SIZE_EXTENDED')) {
	define('PR_ASSOC_MESSAGE_SIZE_EXTENDED', 0x66B40014);
}
if (!defined('PR_FOLDER_PATHNAME')) {
	define('PR_FOLDER_PATHNAME', 0x66B5001E);
}
if (!defined('PR_CODE_PAGE_ID')) {
	define('PR_CODE_PAGE_ID', 0x66C30003);
}
if (!defined('PR_LATEST_PST_ENSURE')) {
	define('PR_LATEST_PST_ENSURE', 0x66FA0003);
}
if (!defined('PR_EMS_AB_MANAGE_DL')) {
	define('PR_EMS_AB_MANAGE_DL', 0x6704000D);
}
if (!defined('PR_SORT_LOCALE_ID')) {
	define('PR_SORT_LOCALE_ID', 0x67050003);
}
if (!defined('PR_EC_SSLKEY_PASS')) {
	define('PR_EC_SSLKEY_PASS', 0x6706001E);
}
if (!defined('PR_LOCAL_COMMIT_TIME')) {
	define('PR_LOCAL_COMMIT_TIME', 0x67090040);
}
if (!defined('PR_LOCAL_COMMIT_TIME_MAX')) {
	define('PR_LOCAL_COMMIT_TIME_MAX', 0x670A0040);
}
if (!defined('PR_DELETED_COUNT_TOTAL')) {
	define('PR_DELETED_COUNT_TOTAL', 0x670B0003);
}
if (!defined('PR_FLAT_URL_NAME')) {
	define('PR_FLAT_URL_NAME', 0x670E001E);
}
if (!defined('PR_ZC_CONTACT_STORE_ENTRYIDS')) {
	define('PR_ZC_CONTACT_STORE_ENTRYIDS', 0x67111102);
}
if (!defined('PR_ZC_CONTACT_FOLDER_ENTRYIDS')) {
	define('PR_ZC_CONTACT_FOLDER_ENTRYIDS', 0x67121102);
}
if (!defined('PR_ZC_CONTACT_FOLDER_NAMES')) {
	define('PR_ZC_CONTACT_FOLDER_NAMES', 0x6713101E);
}
if (!defined('PR_EC_SERVER_VERSION')) {
	define('PR_EC_SERVER_VERSION', 0x6716001E);
}
if (!defined('PR_QUOTA_WARNING_THRESHOLD')) {
	define('PR_QUOTA_WARNING_THRESHOLD', 0x67210003);
}
if (!defined('PR_QUOTA_SEND_THRESHOLD')) {
	define('PR_QUOTA_SEND_THRESHOLD', 0x67220003);
}
if (!defined('PR_QUOTA_RECEIVE_THRESHOLD')) {
	define('PR_QUOTA_RECEIVE_THRESHOLD', 0x67230003);
}
if (!defined('PR_EC_MESSAGE_BCC_ME')) {
	define('PR_EC_MESSAGE_BCC_ME', 0x6725000B);
}
if (!defined('PR_MANAGED_FOLDER_INFORMATION')) {
	define('PR_MANAGED_FOLDER_INFORMATION', 0x672D0003);
}
if (!defined('PR_MANAGED_FOLDER_SIZE')) {
	define('PR_MANAGED_FOLDER_SIZE', 0x67300003);
}
if (!defined('PR_MANAGED_FOLDER_STORAGE_QUOTA')) {
	define('PR_MANAGED_FOLDER_STORAGE_QUOTA', 0x67310003);
}
if (!defined('PR_MANAGED_FOLDER_ID')) {
	define('PR_MANAGED_FOLDER_ID', 0x6732001E);
}
if (!defined('PR_MANAGED_FOLDER_COMMENT')) {
	define('PR_MANAGED_FOLDER_COMMENT', 0x6733001E);
}
if (!defined('PR_DAM_ORIG_MSG_SVREID')) {
	define('PR_DAM_ORIG_MSG_SVREID', 0x67410102);
}
if (!defined('PR_RULE_FOLDER_FID')) {
	define('PR_RULE_FOLDER_FID', 0x67420014);
}
if (!defined('PR_INTERNET_ARTICLE_NUMBER_NEXT')) {
	define('PR_INTERNET_ARTICLE_NUMBER_NEXT', 0x67510003);
}
if (!defined('PR_EC_OUTOFOFFICE')) {
	define('PR_EC_OUTOFOFFICE', 0x67600003);
}
if (!defined('PR_EC_OUTOFOFFICE_MSG')) {
	define('PR_EC_OUTOFOFFICE_MSG', 0x6761001E);
}
if (!defined('PR_EC_OUTOFOFFICE_SUBJECT')) {
	define('PR_EC_OUTOFOFFICE_SUBJECT', 0x6762001E);
}
if (!defined('PR_EC_OUTOFOFFICE_FROM')) {
	define('PR_EC_OUTOFOFFICE_FROM', 0x67630040);
}
if (!defined('PR_EC_OUTOFOFFICE_UNTIL')) {
	define('PR_EC_OUTOFOFFICE_UNTIL', 0x67640040);
}
if (!defined('PR_EC_ALLOW_EXTERNAL')) {
	define('PR_EC_ALLOW_EXTERNAL', 0x6765000B);
}
if (!defined('PR_EC_EXTERNAL_AUDIENCE')) {
	define('PR_EC_EXTERNAL_AUDIENCE', 0x6766000B);
}
if (!defined('PR_EC_EXTERNAL_REPLY')) {
	define('PR_EC_EXTERNAL_REPLY', 0x6767001E);
}
if (!defined('PR_EC_EXTERNAL_SUBJECT')) {
	define('PR_EC_EXTERNAL_SUBJECT', 0x6768001E);
}
if (!defined('PR_EC_WEBACCESS_SETTINGS_JSON')) {
	define('PR_EC_WEBACCESS_SETTINGS_JSON', 0x6772001E);
}
if (!defined('PR_EC_RECIPIENT_HISTORY_JSON')) {
	define('PR_EC_RECIPIENT_HISTORY_JSON', 0x6773001E);
}
if (!defined('PR_EC_WEBAPP_PERSISTENT_SETTINGS_JSON')) {
	define('PR_EC_WEBAPP_PERSISTENT_SETTINGS_JSON', 0x6774001E);
}
if (!defined('PR_EC_OUTGOING_FLAGS')) {
	define('PR_EC_OUTGOING_FLAGS', 0x67800003);
}
if (!defined('PR_EC_IMAP_ID')) {
	define('PR_EC_IMAP_ID', 0x67820003);
}
if (!defined('PR_EC_IMAP_SUBSCRIBED')) {
	define('PR_EC_IMAP_SUBSCRIBED', 0x67840102);
}
if (!defined('PR_EC_IMAP_MAX_ID')) {
	define('PR_EC_IMAP_MAX_ID', 0x67850003);
}
if (!defined('PR_EC_CLIENT_SUBMIT_DATE')) {
	define('PR_EC_CLIENT_SUBMIT_DATE', 0x67860040);
}
if (!defined('PR_EC_MESSAGE_DELIVERY_DATE')) {
	define('PR_EC_MESSAGE_DELIVERY_DATE', 0x67870040);
}
if (!defined('PR_EC_IMAP_EMAIL_SIZE')) {
	define('PR_EC_IMAP_EMAIL_SIZE', 0x678D0003);
}
if (!defined('PR_EC_IMAP_BODY')) {
	define('PR_EC_IMAP_BODY', 0x678E001E);
}
if (!defined('PR_EC_IMAP_BODYSTRUCTURE')) {
	define('PR_EC_IMAP_BODYSTRUCTURE', 0x678F001E);
}
if (!defined('PR_ASSOCIATED')) {
	define('PR_ASSOCIATED', 0x67AA000B);
}
if (!defined('PR_EC_ENABLED_FEATURES_L')) {
	define('PR_EC_ENABLED_FEATURES_L', 0x67B30003);
}
if (!defined('PR_LTP_ROW_ID')) {
	define('PR_LTP_ROW_ID', 0x67F20003);
}
if (!defined('PR_LTP_ROW_VER')) {
	define('PR_LTP_ROW_VER', 0x67F30003);
}
if (!defined('PR_PST_PASSWORD')) {
	define('PR_PST_PASSWORD', 0x67FF0003);
}
if (!defined('PR_OAB_NAME')) {
	define('PR_OAB_NAME', 0x6800001E);
}
if (!defined('PR_OAB_SEQUENCE')) {
	define('PR_OAB_SEQUENCE', 0x68010003);
}
if (!defined('PR_RW_RULES_STREAM')) {
	define('PR_RW_RULES_STREAM', 0x68020102);
}
if (!defined('PR_SENDER_TELEPHONE_NUMBER')) {
	define('PR_SENDER_TELEPHONE_NUMBER', 0x6802001E);
}
if (!defined('PR_SENDER_TELEPHONE_NUMBER_A')) {
	define('PR_SENDER_TELEPHONE_NUMBER_A', 0x6802001E);
}
if (!defined('PR_OAB_CONTAINER_GUID')) {
	define('PR_OAB_CONTAINER_GUID', 0x6802001E);
}
if (!defined('PR_OAB_MESSAGE_CLASS')) {
	define('PR_OAB_MESSAGE_CLASS', 0x68030003);
}
if (!defined('PR_OAB_DN')) {
	define('PR_OAB_DN', 0x6804001E);
}
if (!defined('PR_OAB_TRUNCATED_PROPS')) {
	define('PR_OAB_TRUNCATED_PROPS', 0x68051003);
}
if (!defined('PR_WB_SF_LAST_USED')) {
	define('PR_WB_SF_LAST_USED', 0x68340003);
}
if (!defined('PR_WB_SF_EXPIRATION')) {
	define('PR_WB_SF_EXPIRATION', 0x683A0003);
}
if (!defined('PR_WB_SF_TEMPLATE_ID')) {
	define('PR_WB_SF_TEMPLATE_ID', 0x68410003);
}
if (!defined('PR_SCHDINFO_BOSS_WANTS_COPY')) {
	define('PR_SCHDINFO_BOSS_WANTS_COPY', 0x6842000B);
}
if (!defined('PR_WB_SF_ID')) {
	define('PR_WB_SF_ID', 0x68420102);
}
if (!defined('PR_SCHDINFO_DONT_MAIL_DELEGATES')) {
	define('PR_SCHDINFO_DONT_MAIL_DELEGATES', 0x6843000B);
}
if (!defined('PR_SCHDINFO_DELEGATE_NAMES')) {
	define('PR_SCHDINFO_DELEGATE_NAMES', 0x6844101E);
}
if (!defined('PR_WB_SF_RECREATE_INFO')) {
	define('PR_WB_SF_RECREATE_INFO', 0x68440102);
}
if (!defined('PR_SCHDINFO_DELEGATE_ENTRYIDS')) {
	define('PR_SCHDINFO_DELEGATE_ENTRYIDS', 0x68451102);
}
if (!defined('PR_WB_SF_DEFINITION')) {
	define('PR_WB_SF_DEFINITION', 0x68450102);
}
if (!defined('PR_GATEWAY_NEEDS_TO_REFRESH')) {
	define('PR_GATEWAY_NEEDS_TO_REFRESH', 0x6846000B);
}
if (!defined('PR_WB_SF_STORAGE_TYPE')) {
	define('PR_WB_SF_STORAGE_TYPE', 0x68460003);
}
if (!defined('PR_FREEBUSY_PUBLISH_START')) {
	define('PR_FREEBUSY_PUBLISH_START', 0x68470003);
}
if (!defined('PR_FREEBUSY_PUBLISH_END')) {
	define('PR_FREEBUSY_PUBLISH_END', 0x68480003);
}
if (!defined('PR_FREEBUSY_EMA')) {
	define('PR_FREEBUSY_EMA', 0x6849001E);
}
if (!defined('PR_WLINK_TYPE')) {
	define('PR_WLINK_TYPE', 0x68490003);
}
if (!defined('PR_SCHDINFO_DELEGATE_NAMES_W')) {
	define('PR_SCHDINFO_DELEGATE_NAMES_W', 0x684A101E);
}
if (!defined('PR_WLINK_FLAGS')) {
	define('PR_WLINK_FLAGS', 0x684A0003);
}
if (!defined('PR_SCHDINFO_BOSS_WANTS_INFO')) {
	define('PR_SCHDINFO_BOSS_WANTS_INFO', 0x684B000B);
}
if (!defined('PR_WLINK_ORDINAL')) {
	define('PR_WLINK_ORDINAL', 0x684B0102);
}
if (!defined('PR_WLINK_ENTRYID')) {
	define('PR_WLINK_ENTRYID', 0x684C0102);
}
if (!defined('PR_WLINK_RECKEY')) {
	define('PR_WLINK_RECKEY', 0x684D0102);
}
if (!defined('PR_WLINK_STORE_ENTRYID')) {
	define('PR_WLINK_STORE_ENTRYID', 0x684E0102);
}
if (!defined('PR_SCHDINFO_MONTHS_MERGED')) {
	define('PR_SCHDINFO_MONTHS_MERGED', 0x684F1003);
}
if (!defined('PR_WLINK_FOLDER_TYPE')) {
	define('PR_WLINK_FOLDER_TYPE', 0x684F0102);
}
if (!defined('PR_SCHDINFO_FREEBUSY_MERGED')) {
	define('PR_SCHDINFO_FREEBUSY_MERGED', 0x68501102);
}
if (!defined('PR_WLINK_GROUP_CLSID')) {
	define('PR_WLINK_GROUP_CLSID', 0x68500102);
}
if (!defined('PR_SCHDINFO_MONTHS_TENTATIVE')) {
	define('PR_SCHDINFO_MONTHS_TENTATIVE', 0x68511003);
}
if (!defined('PR_WLINK_GROUP_NAME')) {
	define('PR_WLINK_GROUP_NAME', 0x6851001E);
}
if (!defined('PR_SCHDINFO_FREEBUSY_TENTATIVE')) {
	define('PR_SCHDINFO_FREEBUSY_TENTATIVE', 0x68521102);
}
if (!defined('PR_WLINK_SECTION')) {
	define('PR_WLINK_SECTION', 0x68520003);
}
if (!defined('PR_SCHDINFO_MONTHS_BUSY')) {
	define('PR_SCHDINFO_MONTHS_BUSY', 0x68531003);
}
if (!defined('PR_WLINK_CALENDAR_COLOR')) {
	define('PR_WLINK_CALENDAR_COLOR', 0x68530003);
}
if (!defined('PR_SCHDINFO_FREEBUSY_BUSY')) {
	define('PR_SCHDINFO_FREEBUSY_BUSY', 0x68541102);
}
if (!defined('PR_WLINK_ABEID')) {
	define('PR_WLINK_ABEID', 0x68540102);
}
if (!defined('PR_SCHDINFO_MONTHS_OOF')) {
	define('PR_SCHDINFO_MONTHS_OOF', 0x68551003);
}
if (!defined('PR_SCHDINFO_FREEBUSY_OOF')) {
	define('PR_SCHDINFO_FREEBUSY_OOF', 0x68561102);
}
if (!defined('PR_FREEBUSY_RANGE_TIMESTAMP')) {
	define('PR_FREEBUSY_RANGE_TIMESTAMP', 0x68680040);
}
if (!defined('PR_FREEBUSY_COUNT_MONTHS')) {
	define('PR_FREEBUSY_COUNT_MONTHS', 0x68690003);
}
if (!defined('PR_SCHDINFO_APPT_TOMBSTONE')) {
	define('PR_SCHDINFO_APPT_TOMBSTONE', 0x686A0102);
}
if (!defined('PR_DELEGATE_FLAGS')) {
	define('PR_DELEGATE_FLAGS', 0x686B1003);
}
if (!defined('PR_SCHDINFO_FREEBUSY')) {
	define('PR_SCHDINFO_FREEBUSY', 0x686C0102);
}
if (!defined('PR_SCHDINFO_AUTO_ACCEPT_APPTS')) {
	define('PR_SCHDINFO_AUTO_ACCEPT_APPTS', 0x686D000B);
}
if (!defined('PR_SCHDINFO_DISALLOW_RECURRING_APPTS')) {
	define('PR_SCHDINFO_DISALLOW_RECURRING_APPTS', 0x686E000B);
}
if (!defined('PR_SCHDINFO_DISALLOW_OVERLAPPING_APPTS')) {
	define('PR_SCHDINFO_DISALLOW_OVERLAPPING_APPTS', 0x686F000B);
}
if (!defined('PR_WLINK_CLIENTID')) {
	define('PR_WLINK_CLIENTID', 0x68900102);
}
if (!defined('PR_WLINK_AB_EXSTOREEID')) {
	define('PR_WLINK_AB_EXSTOREEID', 0x68910102);
}
if (!defined('PR_WLINK_RO_GROUP_TYPE')) {
	define('PR_WLINK_RO_GROUP_TYPE', 0x68920003);
}
if (!defined('PR_SECURITY_FLAGS')) {
	define('PR_SECURITY_FLAGS', 0x6E010003);
}
if (!defined('PR_VD_BINARY')) {
	define('PR_VD_BINARY', 0x70010102);
}
if (!defined('PR_VD_STRINGS')) {
	define('PR_VD_STRINGS', 0x7002001E);
}
if (!defined('PR_VD_NAME')) {
	define('PR_VD_NAME', 0x7006001E);
}
if (!defined('PR_VD_VERSION')) {
	define('PR_VD_VERSION', 0x70070003);
}
if (!defined('PR_FAV_DISPLAY_NAME')) {
	define('PR_FAV_DISPLAY_NAME', 0x7C00001E);
}
if (!defined('PR_FAV_DISPLAY_ALIAS')) {
	define('PR_FAV_DISPLAY_ALIAS', 0x7C01001E);
}
if (!defined('PR_FAV_PUBLIC_SOURCE_KEY')) {
	define('PR_FAV_PUBLIC_SOURCE_KEY', 0x7C020102);
}
if (!defined('PR_OST_OSTID')) {
	define('PR_OST_OSTID', 0x7C040102);
}
if (!defined('PR_OFFLINE_FOLDER')) {
	define('PR_OFFLINE_FOLDER', 0x7C050102);
}
if (!defined('PR_ROAMING_DATATYPES')) {
	define('PR_ROAMING_DATATYPES', 0x7C060003);
}
if (!defined('PR_ROAMING_DICTIONARY')) {
	define('PR_ROAMING_DICTIONARY', 0x7C070102);
}
if (!defined('PR_ROAMING_BINARYSTREAM')) {
	define('PR_ROAMING_BINARYSTREAM', 0x7C090102);
}
if (!defined('PR_STORE_SLOWLINK')) {
	define('PR_STORE_SLOWLINK', 0x7C0A000B);
}
if (!defined('PR_OSC_SYNC_ENABLEDONSERVER')) {
	define('PR_OSC_SYNC_ENABLEDONSERVER', 0x7C24000B);
}
if (!defined('PR_FORCE_USE_ENTRYID_SERVER')) {
	define('PR_FORCE_USE_ENTRYID_SERVER', 0x7CFE000B);
}
if (!defined('PR_PROFILE_MDB_DN')) {
	define('PR_PROFILE_MDB_DN', 0x7CFF001E);
}
if (!defined('PR_FAV_AUTOSUBFOLDERS')) {
	define('PR_FAV_AUTOSUBFOLDERS', 0x7D010003);
}
if (!defined('PR_PROCESSED')) {
	define('PR_PROCESSED', 0x7D01000B);
}
if (!defined('PR_FAV_PARENT_SOURCE_KEY')) {
	define('PR_FAV_PARENT_SOURCE_KEY', 0x7D020102);
}
if (!defined('PR_FAV_LEVEL_MASK')) {
	define('PR_FAV_LEVEL_MASK', 0x7D030003);
}
if (!defined('PR_FAV_KNOWN_SUBS')) {
	define('PR_FAV_KNOWN_SUBS', 0x7D040102);
}
if (!defined('PR_FAV_GUID_MAP')) {
	define('PR_FAV_GUID_MAP', 0x7D050102);
}
if (!defined('PR_FAV_KNOWN_DELS_OLD')) {
	define('PR_FAV_KNOWN_DELS_OLD', 0x7D060102);
}
if (!defined('PR_FAV_INHERIT_AUTO')) {
	define('PR_FAV_INHERIT_AUTO', 0x7D070003);
}
if (!defined('PR_FAV_DEL_SUBS')) {
	define('PR_FAV_DEL_SUBS', 0x7D080102);
}
if (!defined('PR_FAV_CONTAINER_CLASS')) {
	define('PR_FAV_CONTAINER_CLASS', 0x7D09001E);
}
if (!defined('PR_EXCEPTION_REPLACETIME')) {
	define('PR_EXCEPTION_REPLACETIME', 0x7FF90040);
}
if (!defined('PR_ATTACHMENT_LINKID')) {
	define('PR_ATTACHMENT_LINKID', 0x7FFA0003);
}
if (!defined('PR_EXCEPTION_STARTTIME')) {
	define('PR_EXCEPTION_STARTTIME', 0x7FFB0040);
}
if (!defined('PR_EXCEPTION_ENDTIME')) {
	define('PR_EXCEPTION_ENDTIME', 0x7FFC0040);
}
if (!defined('PR_ATTACHMENT_FLAGS')) {
	define('PR_ATTACHMENT_FLAGS', 0x7FFD0003);
}
if (!defined('PR_ATTACHMENT_HIDDEN')) {
	define('PR_ATTACHMENT_HIDDEN', 0x7FFE000B);
}
if (!defined('PR_ATTACHMENT_CONTACTPHOTO')) {
	define('PR_ATTACHMENT_CONTACTPHOTO', 0x7FFF000B);
}
if (!defined('PR_EMS_AB_FOLDER_PATHNAME')) {
	define('PR_EMS_AB_FOLDER_PATHNAME', 0x8004001E);
}
if (!defined('PR_EMS_AB_MANAGER')) {
	define('PR_EMS_AB_MANAGER', 0x8005000D);
}
if (!defined('PR_EMS_AB_MANAGER_T')) {
	define('PR_EMS_AB_MANAGER_T', 0x8005001E);
}
if (!defined('PR_EMS_AB_HOME_MDB')) {
	define('PR_EMS_AB_HOME_MDB', 0x8006001E);
}
if (!defined('PR_EMS_AB_HOME_MDB_A')) {
	define('PR_EMS_AB_HOME_MDB_A', 0x8006001E);
}
if (!defined('PR_EMS_AB_IS_MEMBER_OF_DL')) {
	define('PR_EMS_AB_IS_MEMBER_OF_DL', 0x8008001E);
}
if (!defined('PR_EMS_AB_MEMBER')) {
	define('PR_EMS_AB_MEMBER', 0x8009000D);
}
if (!defined('PR_EMS_AB_OWNER')) {
	define('PR_EMS_AB_OWNER', 0x800C0102);
}
if (!defined('PR_EMS_AB_OWNER_O')) {
	define('PR_EMS_AB_OWNER_O', 0x800C000D);
}
if (!defined('PR_EMS_AB_REPORTS')) {
	define('PR_EMS_AB_REPORTS', 0x800E000D);
}
if (!defined('PR_EMS_AB_REPORTS_MV')) {
	define('PR_EMS_AB_REPORTS_MV', 0x800E1102);
}
if (!defined('PR_EMS_AB_PROXY_ADDRESSES')) {
	define('PR_EMS_AB_PROXY_ADDRESSES', 0x800F101E);
}
if (!defined('PR_EMS_AB_PROXY_ADDRESSES_A')) {
	define('PR_EMS_AB_PROXY_ADDRESSES_A', 0x800F101E);
}
if (!defined('PR_EMS_AB_PROXY_ADDRESSES_MV')) {
	define('PR_EMS_AB_PROXY_ADDRESSES_MV', 0x800F101E);
}
if (!defined('PR_EMS_AB_TARGET_ADDRESS')) {
	define('PR_EMS_AB_TARGET_ADDRESS', 0x8011001E);
}
if (!defined('PR_EMS_AB_PUBLIC_DELEGATES')) {
	define('PR_EMS_AB_PUBLIC_DELEGATES', 0x8015000D);
}
if (!defined('PR_EMS_AB_OWNER_BL_O')) {
	define('PR_EMS_AB_OWNER_BL_O', 0x8024000D);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_1')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_1', 0x802D001E);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_2')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_2', 0x802E001E);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_3')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_3', 0x802F001E);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_4')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_4', 0x8030001E);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_5')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_5', 0x8031001E);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_6')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_6', 0x8032001E);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_7')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_7', 0x8033001E);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_8')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_8', 0x8034001E);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_9')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_9', 0x8035001E);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_10')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_10', 0x8036001E);
}
if (!defined('PR_EMS_AB_OBJ_DIST_NAME')) {
	define('PR_EMS_AB_OBJ_DIST_NAME', 0x803C001E);
}
if (!defined('PR_EMS_AB_DELIV_CONT_LENGTH')) {
	define('PR_EMS_AB_DELIV_CONT_LENGTH', 0x806A0003);
}
if (!defined('PR_EMS_AB_DL_MEM_SUBMIT_PERMS_BL_O')) {
	define('PR_EMS_AB_DL_MEM_SUBMIT_PERMS_BL_O', 0x8073000D);
}
if (!defined('PR_EMS_AB_NETWORK_ADDRESS')) {
	define('PR_EMS_AB_NETWORK_ADDRESS', 0x8170101E);
}
if (!defined('PR_EMS_AB_NETWORK_ADDRESS_A')) {
	define('PR_EMS_AB_NETWORK_ADDRESS_A', 0x8170101E);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_11')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_11', 0x8C57001E);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_12')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_12', 0x8C58001E);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_13')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_13', 0x8C59001E);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_14')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_14', 0x8C60001E);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_15')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_15', 0x8C61001E);
}
if (!defined('PR_EMS_AB_X509_CERT')) {
	define('PR_EMS_AB_X509_CERT', 0x8C6A1102);
}
if (!defined('PR_EMS_AB_OBJECT_GUID')) {
	define('PR_EMS_AB_OBJECT_GUID', 0x8C6D0102);
}
if (!defined('PR_EMS_AB_PHONETIC_GIVEN_NAME')) {
	define('PR_EMS_AB_PHONETIC_GIVEN_NAME', 0x8C8E001E);
}
if (!defined('PR_EMS_AB_PHONETIC_SURNAME')) {
	define('PR_EMS_AB_PHONETIC_SURNAME', 0x8C8F001E);
}
if (!defined('PR_EMS_AB_PHONETIC_DEPARTMENT_NAME')) {
	define('PR_EMS_AB_PHONETIC_DEPARTMENT_NAME', 0x8C90001E);
}
if (!defined('PR_EMS_AB_PHONETIC_COMPANY_NAME')) {
	define('PR_EMS_AB_PHONETIC_COMPANY_NAME', 0x8C91001E);
}
if (!defined('PR_EMS_AB_PHONETIC_DISPLAY_NAME')) {
	define('PR_EMS_AB_PHONETIC_DISPLAY_NAME', 0x8C92001E);
}
if (!defined('PR_EMS_AB_PHONETIC_DISPLAY_NAME_A')) {
	define('PR_EMS_AB_PHONETIC_DISPLAY_NAME_A', 0x8C92001E);
}
if (!defined('PR_EMS_AB_DISPLAY_TYPE_EX')) {
	define('PR_EMS_AB_DISPLAY_TYPE_EX', 0x8C930003);
}
if (!defined('PR_EMS_AB_HAB_SHOW_IN_DEPARTMENTS')) {
	define('PR_EMS_AB_HAB_SHOW_IN_DEPARTMENTS', 0x8C94000D);
}
if (!defined('PR_EMS_AB_ROOM_CONTAINERS')) {
	define('PR_EMS_AB_ROOM_CONTAINERS', 0x8C96101E);
}
if (!defined('PR_EMS_AB_HAB_DEPARTMENT_MEMBERS')) {
	define('PR_EMS_AB_HAB_DEPARTMENT_MEMBERS', 0x8C97000D);
}
if (!defined('PR_EMS_AB_HAB_ROOT_DEPARTMENT')) {
	define('PR_EMS_AB_HAB_ROOT_DEPARTMENT', 0x8C98001E);
}
if (!defined('PR_EMS_AB_HAB_PARENT_DEPARTMENT')) {
	define('PR_EMS_AB_HAB_PARENT_DEPARTMENT', 0x8C99000D);
}
if (!defined('PR_EMS_AB_HAB_CHILD_DEPARTMENTS')) {
	define('PR_EMS_AB_HAB_CHILD_DEPARTMENTS', 0x8C9A000D);
}
if (!defined('PR_EMS_AB_THUMBNAIL_PHOTO')) {
	define('PR_EMS_AB_THUMBNAIL_PHOTO', 0x8C9E0102);
}
if (!defined('PR_EMS_AB_HAB_SENIORITY_INDEX')) {
	define('PR_EMS_AB_HAB_SENIORITY_INDEX', 0x8CA00003);
}
if (!defined('PR_EMS_AB_ORG_UNIT_ROOT_DN')) {
	define('PR_EMS_AB_ORG_UNIT_ROOT_DN', 0x8CA8001E);
}
if (!defined('PR_EMS_AB_DL_SENDER_HINT_TRANSLATIONS')) {
	define('PR_EMS_AB_DL_SENDER_HINT_TRANSLATIONS', 0x8CAC101E);
}
if (!defined('PR_EMS_AB_ENABLE_MODERATION')) {
	define('PR_EMS_AB_ENABLE_MODERATION', 0x8CB5000B);
}
if (!defined('PR_EMS_AB_UM_SPOKEN_NAME')) {
	define('PR_EMS_AB_UM_SPOKEN_NAME', 0x8CC20102);
}
if (!defined('PR_EMS_AB_AUTH_ORIG')) {
	define('PR_EMS_AB_AUTH_ORIG', 0x8CD8000D);
}
if (!defined('PR_EMS_AB_UNAUTH_ORIG')) {
	define('PR_EMS_AB_UNAUTH_ORIG', 0x8CD9000D);
}
if (!defined('PR_EMS_AB_DL_MEM_SUBMIT_PERMS')) {
	define('PR_EMS_AB_DL_MEM_SUBMIT_PERMS', 0x8CDA000D);
}
if (!defined('PR_EMS_AB_DL_MEM_REJECT_PERMS')) {
	define('PR_EMS_AB_DL_MEM_REJECT_PERMS', 0x8CDB000D);
}
if (!defined('PR_EMS_AB_HAB_IS_HIERARCHICAL_GROUP')) {
	define('PR_EMS_AB_HAB_IS_HIERARCHICAL_GROUP', 0x8CDD000B);
}
if (!defined('PR_EMS_AB_DL_TOTAL_MEMBER_COUNT')) {
	define('PR_EMS_AB_DL_TOTAL_MEMBER_COUNT', 0x8CE20003);
}
if (!defined('PR_EMS_AB_DL_EXTERNAL_MEMBER_COUNT')) {
	define('PR_EMS_AB_DL_EXTERNAL_MEMBER_COUNT', 0x8CE30003);
}
if (!defined('PR_EMS_AB_IS_MASTER')) {
	define('PR_EMS_AB_IS_MASTER', 0xFFFB000B);
}
if (!defined('PR_EMS_AB_PARENT_ENTRYID')) {
	define('PR_EMS_AB_PARENT_ENTRYID', 0xFFFC0102);
}
if (!defined('PR_EMS_AB_CONTAINERID')) {
	define('PR_EMS_AB_CONTAINERID', 0xFFFD0003);
}
if (!defined('PidLidCategories')) {
	define('PidLidCategories', 0x00002328);
}
if (!defined('PidLidFileAs')) {
	define('PidLidFileAs', 0x00008005);
}
if (!defined('PidLidHomeAddress')) {
	define('PidLidHomeAddress', 0x0000801A);
}
if (!defined('PidLidBusinessAddress')) {
	define('PidLidBusinessAddress', 0x0000801B);
}
if (!defined('PidLidOtherAddress')) {
	define('PidLidOtherAddress', 0x0000801C);
}
if (!defined('PidLidMailingAdress')) {
	define('PidLidMailingAdress', 0x00008022);
}
if (!defined('PidLidPostalAddressIndex')) {
	define('PidLidPostalAddressIndex', 0x00008022);
}
if (!defined('PidLidBusinessCardDisplayDefinition')) {
	define('PidLidBusinessCardDisplayDefinition', 0x00008040);
}
if (!defined('PidLidWorkAddressStreet')) {
	define('PidLidWorkAddressStreet', 0x00008045);
}
if (!defined('PidLidWorkAddressCity')) {
	define('PidLidWorkAddressCity', 0x00008046);
}
if (!defined('PidLidWorkAddressState')) {
	define('PidLidWorkAddressState', 0x00008047);
}
if (!defined('PidLidWorkAddressPostalCode')) {
	define('PidLidWorkAddressPostalCode', 0x00008048);
}
if (!defined('PidLidWorkAddressCountry')) {
	define('PidLidWorkAddressCountry', 0x00008049);
}
if (!defined('PidLidWorkAddressPostOfficeBox')) {
	define('PidLidWorkAddressPostOfficeBox', 0x0000804A);
}
if (!defined('PidLidContactUserField1')) {
	define('PidLidContactUserField1', 0x0000804F);
}
if (!defined('PidLidContactUserField2')) {
	define('PidLidContactUserField2', 0x00008050);
}
if (!defined('PidLidContactUserField3')) {
	define('PidLidContactUserField3', 0x00008051);
}
if (!defined('PidLidContactUserField4')) {
	define('PidLidContactUserField4', 0x00008052);
}
if (!defined('PidLidInstantMessagingAddress')) {
	define('PidLidInstantMessagingAddress', 0x00008062);
}
if (!defined('PidLidEmail1DisplayName')) {
	define('PidLidEmail1DisplayName', 0x00008080);
}
if (!defined('PidLidEmail1AddressType')) {
	define('PidLidEmail1AddressType', 0x00008082);
}
if (!defined('PidLidEmail1EmailAddress')) {
	define('PidLidEmail1EmailAddress', 0x00008083);
}
if (!defined('PidLidEmail2DisplayName')) {
	define('PidLidEmail2DisplayName', 0x00008090);
}
if (!defined('PidLidEmail2AddressType')) {
	define('PidLidEmail2AddressType', 0x00008092);
}
if (!defined('PidLidEmail2EmailAddress')) {
	define('PidLidEmail2EmailAddress', 0x00008093);
}
if (!defined('PidLidEmail3DisplayName')) {
	define('PidLidEmail3DisplayName', 0x000080A0);
}
if (!defined('PidLidEmail3AddressType')) {
	define('PidLidEmail3AddressType', 0x000080A2);
}
if (!defined('PidLidEmail3EmailAddress')) {
	define('PidLidEmail3EmailAddress', 0x000080A3);
}
if (!defined('PidLidFreeBusyLocation')) {
	define('PidLidFreeBusyLocation', 0x000080D8);
}
if (!defined('PidLidTaskStatus')) {
	define('PidLidTaskStatus', 0x00008101);
}
if (!defined('PidLidPercentComplete')) {
	define('PidLidPercentComplete', 0x00008102);
}
if (!defined('PidLidTaskStartDate')) {
	define('PidLidTaskStartDate', 0x00008104);
}
if (!defined('PidLidTaskDueDate')) {
	define('PidLidTaskDueDate', 0x00008105);
}
if (!defined('PidLidTaskDateCompleted')) {
	define('PidLidTaskDateCompleted', 0x0000810F);
}
if (!defined('PidLidTaskActualEffort')) {
	define('PidLidTaskActualEffort', 0x00008110);
}
if (!defined('PidLidTaskEstimatedEffort')) {
	define('PidLidTaskEstimatedEffort', 0x00008111);
}
if (!defined('PidLidTaskRecurrence')) {
	define('PidLidTaskRecurrence', 0x00008116);
}
if (!defined('PidLidTaskComplete')) {
	define('PidLidTaskComplete', 0x0000811C);
}
if (!defined('PidLidTaskOwner')) {
	define('PidLidTaskOwner', 0x0000811F);
}
if (!defined('PidLidTaskFRecurring')) {
	define('PidLidTaskFRecurring', 0x00008126);
}
if (!defined('PidLidSendMeetingAsIcal')) {
	define('PidLidSendMeetingAsIcal', 0x00008200);
}
if (!defined('PidLidAppointmentSequence')) {
	define('PidLidAppointmentSequence', 0x00008201);
}
if (!defined('PidLidBusyStatus')) {
	define('PidLidBusyStatus', 0x00008205);
}
if (!defined('PidLidLocation')) {
	define('PidLidLocation', 0x00008208);
}
if (!defined('PidLidAppointmentStartWhole')) {
	define('PidLidAppointmentStartWhole', 0x0000820D);
}
if (!defined('PidLidAppointmentEndWhole')) {
	define('PidLidAppointmentEndWhole', 0x0000820E);
}
if (!defined('PidLidAppointmentDuration')) {
	define('PidLidAppointmentDuration', 0x00008213);
}
if (!defined('PidLidAppointmentSubType')) {
	define('PidLidAppointmentSubType', 0x00008215);
}
if (!defined('PidLidAppointmentRecur')) {
	define('PidLidAppointmentRecur', 0x00008216);
}
if (!defined('PidLidAppointmentStateFlags')) {
	define('PidLidAppointmentStateFlags', 0x00008217);
}
if (!defined('PidLidResponseStatus')) {
	define('PidLidResponseStatus', 0x00008218);
}
if (!defined('PidLidAppointmentReplyTime')) {
	define('PidLidAppointmentReplyTime', 0x00008220);
}
if (!defined('PidLidRecurring')) {
	define('PidLidRecurring', 0x00008223);
}
if (!defined('PidLidIntendedBusyStatus')) {
	define('PidLidIntendedBusyStatus', 0x00008224);
}
if (!defined('PidLidExceptionReplaceTime')) {
	define('PidLidExceptionReplaceTime', 0x00008228);
}
if (!defined('PidLidFInvited')) {
	define('PidLidFInvited', 0x00008229);
}
if (!defined('PidLidAppointmentReplyName')) {
	define('PidLidAppointmentReplyName', 0x00008230);
}
if (!defined('PidLidRecurrenceType')) {
	define('PidLidRecurrenceType', 0x00008231);
}
if (!defined('PidLidRecurrencePattern')) {
	define('PidLidRecurrencePattern', 0x00008232);
}
if (!defined('PidLidTimeZoneStruct')) {
	define('PidLidTimeZoneStruct', 0x00008233);
}
if (!defined('PidLidTimeZoneDescription')) {
	define('PidLidTimeZoneDescription', 0x00008234);
}
if (!defined('PidLidClipStart')) {
	define('PidLidClipStart', 0x00008235);
}
if (!defined('PidLidClipEnd')) {
	define('PidLidClipEnd', 0x00008236);
}
if (!defined('PidLidAllAttendeesString')) {
	define('PidLidAllAttendeesString', 0x00008238);
}
if (!defined('PidLidToAttendeesString')) {
	define('PidLidToAttendeesString', 0x0000823B);
}
if (!defined('PidLidCcAttendeesString')) {
	define('PidLidCcAttendeesString', 0x0000823C);
}
if (!defined('PidLidAppointmentProposedStartWhole')) {
	define('PidLidAppointmentProposedStartWhole', 0x00008250);
}
if (!defined('PidLidAppointmentProposedEndWhole')) {
	define('PidLidAppointmentProposedEndWhole', 0x00008251);
}
if (!defined('PidLidAppointmentCounterProposal')) {
	define('PidLidAppointmentCounterProposal', 0x00008257);
}
if (!defined('PidLidAppointmentNotAllowPropose')) {
	define('PidLidAppointmentNotAllowPropose', 0x0000825A);
}
if (!defined('PidLidAppointmentTimeZoneDefinitionStartDisplay')) {
	define('PidLidAppointmentTimeZoneDefinitionStartDisplay', 0x0000825E);
}
if (!defined('PidLidAppointmentTimeZoneDefinitionEndDisplay')) {
	define('PidLidAppointmentTimeZoneDefinitionEndDisplay', 0x0000825F);
}
if (!defined('PidLidAppointmentTimeZoneDefinitionRecur')) {
	define('PidLidAppointmentTimeZoneDefinitionRecur', 0x00008260);
}
if (!defined('PidLidAttendeeCriticalChange')) {
	define('PidLidAttendeeCriticalChange', 0x00000001);
}
if (!defined('PidLidWhere')) {
	define('PidLidWhere', 0x00000002);
}
if (!defined('PidLidGlobalObjectId')) {
	define('PidLidGlobalObjectId', 0x00000003);
}
if (!defined('PidLidIsSilent')) {
	define('PidLidIsSilent', 0x00000004);
}
if (!defined('PidLidIsRecurring')) {
	define('PidLidIsRecurring', 0x00000005);
}
if (!defined('PidLidIsException')) {
	define('PidLidIsException', 0x0000000A);
}
if (!defined('PidLidTimeZone')) {
	define('PidLidTimeZone', 0x0000000C);
}
if (!defined('PidLidStartRecurrenceTime')) {
	define('PidLidStartRecurrenceTime', 0x0000000E);
}
if (!defined('PidLidMonthOfYearMask')) {
	define('PidLidMonthOfYearMask', 0x00000017);
}
if (!defined('PidLidOwnerCriticalChange')) {
	define('PidLidOwnerCriticalChange', 0x0000001A);
}
if (!defined('PidLidCleanGlobalObjectId')) {
	define('PidLidCleanGlobalObjectId', 0x00000023);
}
if (!defined('PidLidMeetingType')) {
	define('PidLidMeetingType', 0x00000026);
}
if (!defined('PidLidReminderDelta')) {
	define('PidLidReminderDelta', 0x00008501);
}
if (!defined('PidLidReminderTime')) {
	define('PidLidReminderTime', 0x00008502);
}
if (!defined('PidLidReminderSet')) {
	define('PidLidReminderSet', 0x00008503);
}
if (!defined('PidLidPrivate')) {
	define('PidLidPrivate', 0x00008506);
}
if (!defined('PidLidSideEffects')) {
	define('PidLidSideEffects', 0x00008510);
}
if (!defined('PidLidSmartNoAttach')) {
	define('PidLidSmartNoAttach', 0x00008514);
}
if (!defined('PidLidCommonStart')) {
	define('PidLidCommonStart', 0x00008516);
}
if (!defined('PidLidCommonEnd')) {
	define('PidLidCommonEnd', 0x00008517);
}
if (!defined('PidLidTaskMode')) {
	define('PidLidTaskMode', 0x00008518);
}
if (!defined('PidLidFlagRequest')) {
	define('PidLidFlagRequest', 0x00008530);
}
if (!defined('PidLidMileage')) {
	define('PidLidMileage', 0x00008534);
}
if (!defined('PidLidBilling')) {
	define('PidLidBilling', 0x00008535);
}
if (!defined('PidLidCompanies')) {
	define('PidLidCompanies', 0x00008539);
}
if (!defined('PidLidReminderSignalTime')) {
	define('PidLidReminderSignalTime', 0x00008560);
}
if (!defined('PidLidToDoTitle')) {
	define('PidLidToDoTitle', 0x000085A4);
}
if (!defined('PidLidInfoPathFromName')) {
	define('PidLidInfoPathFromName', 0x000085B1);
}
if (!defined('PidLidClassified')) {
	define('PidLidClassified', 0x000085B5);
}
if (!defined('PidLidClassification')) {
	define('PidLidClassification', 0x000085B6);
}
if (!defined('PidLidClassificationDescription')) {
	define('PidLidClassificationDescription', 0x000085B7);
}
if (!defined('PidLidClassificationGuid')) {
	define('PidLidClassificationGuid', 0x000085B8);
}
if (!defined('PidLidClassificationKeep')) {
	define('PidLidClassificationKeep', 0x000085BA);
}
if (!defined('ecSuccess')) {
	define('ecSuccess', 0x00000000);
}
if (!defined('MAPI_E_UNBINDSUCCESS')) {
	define('MAPI_E_UNBINDSUCCESS', 0x00000001);
}
if (!defined('MAPI_E_USER_ABORT')) {
	define('MAPI_E_USER_ABORT', 0x00000001);
}
if (!defined('MAPI_E_FAILURE')) {
	define('MAPI_E_FAILURE', 0x00000002);
}
if (!defined('MAPI_E_LOGON_FAILURE')) {
	define('MAPI_E_LOGON_FAILURE', 0x00000003);
}
if (!defined('MAPI_E_DISK_FULL')) {
	define('MAPI_E_DISK_FULL', 0x00000004);
}
if (!defined('MAPI_E_INSUFFICIENT_MEMORY')) {
	define('MAPI_E_INSUFFICIENT_MEMORY', 0x00000005);
}
if (!defined('MAPI_E_ACCESS_DENIED')) {
	define('MAPI_E_ACCESS_DENIED', 0x00000006);
}
if (!defined('MAPI_E_TOO_MANY_SESSIONS')) {
	define('MAPI_E_TOO_MANY_SESSIONS', 0x00000008);
}
if (!defined('MAPI_E_TOO_MANY_FILES')) {
	define('MAPI_E_TOO_MANY_FILES', 0x00000009);
}
if (!defined('MAPI_E_TOO_MANY_RECIPIENTS')) {
	define('MAPI_E_TOO_MANY_RECIPIENTS', 0x0000000A);
}
if (!defined('MAPI_E_ATTACHMENT_NOT_FOUND')) {
	define('MAPI_E_ATTACHMENT_NOT_FOUND', 0x0000000B);
}
if (!defined('MAPI_E_ATTACHMENT_OPEN_FAILURE')) {
	define('MAPI_E_ATTACHMENT_OPEN_FAILURE', 0x0000000C);
}
if (!defined('MAPI_E_ATTACHMENT_WRITE_FAILURE')) {
	define('MAPI_E_ATTACHMENT_WRITE_FAILURE', 0x0000000D);
}
if (!defined('MAPI_E_UNKNOWN_RECIPIENT')) {
	define('MAPI_E_UNKNOWN_RECIPIENT', 0x0000000E);
}
if (!defined('MAPI_E_BAD_RECIPTYPE')) {
	define('MAPI_E_BAD_RECIPTYPE', 0x0000000F);
}
if (!defined('MAPI_E_NO_MESSAGES')) {
	define('MAPI_E_NO_MESSAGES', 0x00000010);
}
if (!defined('MAPI_E_INVALID_MESSAGE')) {
	define('MAPI_E_INVALID_MESSAGE', 0x00000011);
}
if (!defined('MAPI_E_TEXT_TOO_LARGE')) {
	define('MAPI_E_TEXT_TOO_LARGE', 0x00000012);
}
if (!defined('MAPI_E_INVALID_SESSION')) {
	define('MAPI_E_INVALID_SESSION', 0x00000013);
}
if (!defined('MAPI_E_TYPE_NOT_SUPPORTED')) {
	define('MAPI_E_TYPE_NOT_SUPPORTED', 0x00000014);
}
if (!defined('MAPI_E_AMBIGUOUS_RECIPIENT')) {
	define('MAPI_E_AMBIGUOUS_RECIPIENT', 0x00000015);
}
if (!defined('MAPI_E_MESSAGE_IN_USE')) {
	define('MAPI_E_MESSAGE_IN_USE', 0x00000016);
}
if (!defined('MAPI_E_NETWORK_FAILURE')) {
	define('MAPI_E_NETWORK_FAILURE', 0x00000017);
}
if (!defined('MAPI_E_INVALID_EDITFIELDS')) {
	define('MAPI_E_INVALID_EDITFIELDS', 0x00000018);
}
if (!defined('MAPI_E_INVALID_RECIPS')) {
	define('MAPI_E_INVALID_RECIPS', 0x00000019);
}
if (!defined('MAPI_E_NOT_SUPPORTED')) {
	define('MAPI_E_NOT_SUPPORTED', 0x0000001A);
}
if (!defined('ecJetError')) {
	define('ecJetError', 0x000003EA);
}
if (!defined('ecUnknownUser')) {
	define('ecUnknownUser', 0x000003EB);
}
if (!defined('ecExiting')) {
	define('ecExiting', 0x000003ED);
}
if (!defined('ecBadConfig')) {
	define('ecBadConfig', 0x000003EE);
}
if (!defined('ecUnknownCodePage')) {
	define('ecUnknownCodePage', 0x000003EF);
}
if (!defined('ecServerOOM')) {
	define('ecServerOOM', 0x000003F0);
}
if (!defined('ecLoginPerm')) {
	define('ecLoginPerm', 0x000003F2);
}
if (!defined('ecDatabaseRolledBack')) {
	define('ecDatabaseRolledBack', 0x000003F3);
}
if (!defined('ecDatabaseCopiedError')) {
	define('ecDatabaseCopiedError', 0x000003F4);
}
if (!defined('ecAuditNotAllowed')) {
	define('ecAuditNotAllowed', 0x000003F5);
}
if (!defined('ecZombieUser')) {
	define('ecZombieUser', 0x000003F6);
}
if (!defined('ecUnconvertableACL')) {
	define('ecUnconvertableACL', 0x000003F7);
}
if (!defined('ecNoFreeJses')) {
	define('ecNoFreeJses', 0x0000044C);
}
if (!defined('ecDifferentJses')) {
	define('ecDifferentJses', 0x0000044D);
}
if (!defined('ecFileRemove')) {
	define('ecFileRemove', 0x0000044F);
}
if (!defined('ecParameterOverflow')) {
	define('ecParameterOverflow', 0x00000450);
}
if (!defined('ecBadVersion')) {
	define('ecBadVersion', 0x00000451);
}
if (!defined('ecTooManyCols')) {
	define('ecTooManyCols', 0x00000452);
}
if (!defined('ecHaveMore')) {
	define('ecHaveMore', 0x00000453);
}
if (!defined('ecDatabaseError')) {
	define('ecDatabaseError', 0x00000454);
}
if (!defined('ecIndexNameTooBig')) {
	define('ecIndexNameTooBig', 0x00000455);
}
if (!defined('ecUnsupportedProp')) {
	define('ecUnsupportedProp', 0x00000456);
}
if (!defined('ecMsgNotSaved')) {
	define('ecMsgNotSaved', 0x00000457);
}
if (!defined('ecUnpubNotif')) {
	define('ecUnpubNotif', 0x00000459);
}
if (!defined('ecDifferentRoot')) {
	define('ecDifferentRoot', 0x0000045B);
}
if (!defined('ecBadFolderName')) {
	define('ecBadFolderName', 0x0000045C);
}
if (!defined('ecAttachOpen')) {
	define('ecAttachOpen', 0x0000045D);
}
if (!defined('ecInvClpsState')) {
	define('ecInvClpsState', 0x0000045E);
}
if (!defined('ecSkipMyChildren')) {
	define('ecSkipMyChildren', 0x0000045F);
}
if (!defined('ecSearchFolder')) {
	define('ecSearchFolder', 0x00000460);
}
if (!defined('ecNotSearchFolder')) {
	define('ecNotSearchFolder', 0x00000461);
}
if (!defined('ecFolderSetReceive')) {
	define('ecFolderSetReceive', 0x00000462);
}
if (!defined('ecNoReceiveFolder')) {
	define('ecNoReceiveFolder', 0x00000463);
}
if (!defined('ecNoDelSubmitMsg')) {
	define('ecNoDelSubmitMsg', 0x00000465);
}
if (!defined('ecInvalidRecips')) {
	define('ecInvalidRecips', 0x00000467);
}
if (!defined('ecNoReplicaHere')) {
	define('ecNoReplicaHere', 0x00000468);
}
if (!defined('ecNoReplicaAvailable')) {
	define('ecNoReplicaAvailable', 0x00000469);
}
if (!defined('ecPublicMDB')) {
	define('ecPublicMDB', 0x0000046A);
}
if (!defined('ecNotPublicMDB')) {
	define('ecNotPublicMDB', 0x0000046B);
}
if (!defined('ecRecordNotFound')) {
	define('ecRecordNotFound', 0x0000046C);
}
if (!defined('ecReplConflict')) {
	define('ecReplConflict', 0x0000046D);
}
if (!defined('ecFxBufferOverrun')) {
	define('ecFxBufferOverrun', 0x00000470);
}
if (!defined('ecFxBufferEmpty')) {
	define('ecFxBufferEmpty', 0x00000471);
}
if (!defined('ecFxPartialValue')) {
	define('ecFxPartialValue', 0x00000472);
}
if (!defined('ecFxNoRoom')) {
	define('ecFxNoRoom', 0x00000473);
}
if (!defined('ecMaxTimeExpired')) {
	define('ecMaxTimeExpired', 0x00000474);
}
if (!defined('ecDstError')) {
	define('ecDstError', 0x00000475);
}
if (!defined('ecMDBNotInit')) {
	define('ecMDBNotInit', 0x00000476);
}
if (!defined('ecWrongServer')) {
	define('ecWrongServer', 0x00000478);
}
if (!defined('ecBufferTooSmall')) {
	define('ecBufferTooSmall', 0x0000047D);
}
if (!defined('ecRequiresRefResolve')) {
	define('ecRequiresRefResolve', 0x0000047E);
}
if (!defined('ecServerPaused')) {
	define('ecServerPaused', 0x0000047F);
}
if (!defined('ecServerBusy')) {
	define('ecServerBusy', 0x00000480);
}
if (!defined('ecNoSuchLogon')) {
	define('ecNoSuchLogon', 0x00000481);
}
if (!defined('ecLoadLibFailed')) {
	define('ecLoadLibFailed', 0x00000482);
}
if (!defined('ecObjAlreadyConfig')) {
	define('ecObjAlreadyConfig', 0x00000483);
}
if (!defined('ecObjNotConfig')) {
	define('ecObjNotConfig', 0x00000484);
}
if (!defined('ecDataLoss')) {
	define('ecDataLoss', 0x00000485);
}
if (!defined('ecMaxSendThreadExceeded')) {
	define('ecMaxSendThreadExceeded', 0x00000488);
}
if (!defined('ecFxErrorMarker')) {
	define('ecFxErrorMarker', 0x00000489);
}
if (!defined('ecNoFreeJtabs')) {
	define('ecNoFreeJtabs', 0x0000048A);
}
if (!defined('ecNotPrivateMDB')) {
	define('ecNotPrivateMDB', 0x0000048B);
}
if (!defined('ecIsintegMDB')) {
	define('ecIsintegMDB', 0x0000048C);
}
if (!defined('ecRecoveryMDBMismatch')) {
	define('ecRecoveryMDBMismatch', 0x0000048D);
}
if (!defined('ecTableMayNotBeDeleted')) {
	define('ecTableMayNotBeDeleted', 0x0000048E);
}
if (!defined('ecSearchFolderScopeViolation')) {
	define('ecSearchFolderScopeViolation', 0x00000490);
}
if (!defined('ecRpcRegisterIf')) {
	define('ecRpcRegisterIf', 0x000004B1);
}
if (!defined('ecRpcListen')) {
	define('ecRpcListen', 0x000004B2);
}
if (!defined('ecRpcFormat')) {
	define('ecRpcFormat', 0x000004B6);
}
if (!defined('ecNoCopyTo')) {
	define('ecNoCopyTo', 0x000004B7);
}
if (!defined('ecNullObject')) {
	define('ecNullObject', 0x000004B9);
}
if (!defined('ecRpcAuthentication')) {
	define('ecRpcAuthentication', 0x000004BC);
}
if (!defined('ecRpcBadAuthenticationLevel')) {
	define('ecRpcBadAuthenticationLevel', 0x000004BD);
}
if (!defined('ecNullCommentRestriction')) {
	define('ecNullCommentRestriction', 0x000004BE);
}
if (!defined('ecRulesLoadError')) {
	define('ecRulesLoadError', 0x000004CC);
}
if (!defined('ecRulesDelivErr')) {
	define('ecRulesDelivErr', 0x000004CD);
}
if (!defined('ecRulesParsingErr')) {
	define('ecRulesParsingErr', 0x000004CE);
}
if (!defined('ecRulesCreateDaeErr')) {
	define('ecRulesCreateDaeErr', 0x000004CF);
}
if (!defined('ecRulesCreateDamErr')) {
	define('ecRulesCreateDamErr', 0x000004D0);
}
if (!defined('ecRulesNoMoveCopyFolder')) {
	define('ecRulesNoMoveCopyFolder', 0x000004D1);
}
if (!defined('ecRulesNoFolderRights')) {
	define('ecRulesNoFolderRights', 0x000004D2);
}
if (!defined('ecMessageTooBig')) {
	define('ecMessageTooBig', 0x000004D4);
}
if (!defined('ecFormNotValid')) {
	define('ecFormNotValid', 0x000004D5);
}
if (!defined('ecNotAuthorized')) {
	define('ecNotAuthorized', 0x000004D6);
}
if (!defined('ecDeleteMessage')) {
	define('ecDeleteMessage', 0x000004D7);
}
if (!defined('ecBounceMessage')) {
	define('ecBounceMessage', 0x000004D8);
}
if (!defined('ecQuotaExceeded')) {
	define('ecQuotaExceeded', 0x000004D9);
}
if (!defined('ecMaxSubmissionExceeded')) {
	define('ecMaxSubmissionExceeded', 0x000004DA);
}
if (!defined('ecMaxAttachmentExceeded')) {
	define('ecMaxAttachmentExceeded', 0x000004DB);
}
if (!defined('ecSendAsDenied')) {
	define('ecSendAsDenied', 0x000004DC);
}
if (!defined('ecShutoffQuotaExceeded')) {
	define('ecShutoffQuotaExceeded', 0x000004DD);
}
if (!defined('ecMaxObjsExceeded')) {
	define('ecMaxObjsExceeded', 0x000004DE);
}
if (!defined('ecClientVerDisallowed')) {
	define('ecClientVerDisallowed', 0x000004DF);
}
if (!defined('ecRpcHttpDisallowed')) {
	define('ecRpcHttpDisallowed', 0x000004E0);
}
if (!defined('ecCachedModeRequired')) {
	define('ecCachedModeRequired', 0x000004E1);
}
if (!defined('ecFolderNotCleanedUp')) {
	define('ecFolderNotCleanedUp', 0x000004E3);
}
if (!defined('ecFmtError')) {
	define('ecFmtError', 0x000004ED);
}
if (!defined('ecNotExpanded')) {
	define('ecNotExpanded', 0x000004F7);
}
if (!defined('ecNotCollapsed')) {
	define('ecNotCollapsed', 0x000004F8);
}
if (!defined('ecLeaf')) {
	define('ecLeaf', 0x000004F9);
}
if (!defined('ecUnregisteredNamedProp')) {
	define('ecUnregisteredNamedProp', 0x000004FA);
}
if (!defined('ecFolderDisabled')) {
	define('ecFolderDisabled', 0x000004FB);
}
if (!defined('ecDomainError')) {
	define('ecDomainError', 0x000004FC);
}
if (!defined('ecNoCreateRight')) {
	define('ecNoCreateRight', 0x000004FF);
}
if (!defined('ecPublicRoot')) {
	define('ecPublicRoot', 0x00000500);
}
if (!defined('ecNoReadRight')) {
	define('ecNoReadRight', 0x00000501);
}
if (!defined('ecNoCreateSubfolderRight')) {
	define('ecNoCreateSubfolderRight', 0x00000502);
}
if (!defined('ecDstNullObject')) {
	define('ecDstNullObject', 0x00000503);
}
if (!defined('ecMsgCycle')) {
	define('ecMsgCycle', 0x00000504);
}
if (!defined('ecTooManyRecips')) {
	define('ecTooManyRecips', 0x00000505);
}
if (!defined('ecVirusScanInProgress')) {
	define('ecVirusScanInProgress', 0x0000050A);
}
if (!defined('ecVirusDetected')) {
	define('ecVirusDetected', 0x0000050B);
}
if (!defined('ecMailboxInTransit')) {
	define('ecMailboxInTransit', 0x0000050C);
}
if (!defined('ecBackupInProgress')) {
	define('ecBackupInProgress', 0x0000050D);
}
if (!defined('ecVirusMessageDeleted')) {
	define('ecVirusMessageDeleted', 0x0000050E);
}
if (!defined('ecInvalidBackupSequence')) {
	define('ecInvalidBackupSequence', 0x0000050F);
}
if (!defined('ecInvalidBackupType')) {
	define('ecInvalidBackupType', 0x00000510);
}
if (!defined('ecTooManyBackupsInProgress')) {
	define('ecTooManyBackupsInProgress', 0x00000511);
}
if (!defined('ecRestoreInProgress')) {
	define('ecRestoreInProgress', 0x00000512);
}
if (!defined('ecDuplicateObject')) {
	define('ecDuplicateObject', 0x00000579);
}
if (!defined('ecObjectNotFound')) {
	define('ecObjectNotFound', 0x0000057A);
}
if (!defined('ecFixupReplyRule')) {
	define('ecFixupReplyRule', 0x0000057B);
}
if (!defined('ecTemplateNotFound')) {
	define('ecTemplateNotFound', 0x0000057C);
}
if (!defined('ecRuleException')) {
	define('ecRuleException', 0x0000057D);
}
if (!defined('ecDSNoSuchObject')) {
	define('ecDSNoSuchObject', 0x0000057E);
}
if (!defined('ecMessageAlreadyTombstoned')) {
	define('ecMessageAlreadyTombstoned', 0x0000057F);
}
if (!defined('ecRequiresRWTransaction')) {
	define('ecRequiresRWTransaction', 0x00000596);
}
if (!defined('ecPaused')) {
	define('ecPaused', 0x0000060E);
}
if (!defined('ecNotPaused')) {
	define('ecNotPaused', 0x0000060F);
}
if (!defined('ecWrongMailbox')) {
	define('ecWrongMailbox', 0x00000648);
}
if (!defined('ecChgPassword')) {
	define('ecChgPassword', 0x0000064C);
}
if (!defined('ecPwdExpired')) {
	define('ecPwdExpired', 0x0000064D);
}
if (!defined('ecInvWkstn')) {
	define('ecInvWkstn', 0x0000064E);
}
if (!defined('ecInvLogonHrs')) {
	define('ecInvLogonHrs', 0x0000064F);
}
if (!defined('ecAcctDisabled')) {
	define('ecAcctDisabled', 0x00000650);
}
if (!defined('ecRuleVersion')) {
	define('ecRuleVersion', 0x000006A4);
}
if (!defined('ecRuleFormat')) {
	define('ecRuleFormat', 0x000006A5);
}
if (!defined('ecRuleSendAsDenied')) {
	define('ecRuleSendAsDenied', 0x000006A6);
}
if (!defined('ecNoServerSupport')) {
	define('ecNoServerSupport', 0x000006B9);
}
if (!defined('ecLockTimedOut')) {
	define('ecLockTimedOut', 0x000006BA);
}
if (!defined('ecObjectLocked')) {
	define('ecObjectLocked', 0x000006BB);
}
if (!defined('ecInvalidLockNamespace')) {
	define('ecInvalidLockNamespace', 0x000006BD);
}
if (!defined('RPC_X_BAD_STUB_DATA')) {
	define('RPC_X_BAD_STUB_DATA', 0x000006F7);
}
if (!defined('ecMessageDeleted')) {
	define('ecMessageDeleted', 0x000007D6);
}
if (!defined('ecProtocolDisabled')) {
	define('ecProtocolDisabled', 0x000007D8);
}
if (!defined('ecClearTextLogonDisabled')) {
	define('ecClearTextLogonDisabled', 0x000007D9);
}
if (!defined('ecRejected')) {
	define('ecRejected', 0x000007EE);
}
if (!defined('ecAmbiguousAlias')) {
	define('ecAmbiguousAlias', 0x0000089A);
}
if (!defined('ecUnknownMailbox')) {
	define('ecUnknownMailbox', 0x0000089B);
}
if (!defined('ecExpReserved')) {
	define('ecExpReserved', 0x000008FC);
}
if (!defined('ecExpParseDepth')) {
	define('ecExpParseDepth', 0x000008FD);
}
if (!defined('ecExpFuncArgType')) {
	define('ecExpFuncArgType', 0x000008FE);
}
if (!defined('ecExpSyntax')) {
	define('ecExpSyntax', 0x000008FF);
}
if (!defined('ecExpBadStrToken')) {
	define('ecExpBadStrToken', 0x00000900);
}
if (!defined('ecExpBadColToken')) {
	define('ecExpBadColToken', 0x00000901);
}
if (!defined('ecExpTypeMismatch')) {
	define('ecExpTypeMismatch', 0x00000902);
}
if (!defined('ecExpOpNotSupported')) {
	define('ecExpOpNotSupported', 0x00000903);
}
if (!defined('ecExpDivByZero')) {
	define('ecExpDivByZero', 0x00000904);
}
if (!defined('ecExpUnaryArgType')) {
	define('ecExpUnaryArgType', 0x00000905);
}
if (!defined('ecNotLocked')) {
	define('ecNotLocked', 0x00000960);
}
if (!defined('ecClientEvent')) {
	define('ecClientEvent', 0x00000961);
}
if (!defined('ecCorruptEvent')) {
	define('ecCorruptEvent', 0x00000965);
}
if (!defined('ecCorruptWatermark')) {
	define('ecCorruptWatermark', 0x00000966);
}
if (!defined('ecEventError')) {
	define('ecEventError', 0x00000967);
}
if (!defined('ecWatermarkError')) {
	define('ecWatermarkError', 0x00000968);
}
if (!defined('ecNonCanonicalACL')) {
	define('ecNonCanonicalACL', 0x00000969);
}
if (!defined('ecMailboxDisabled')) {
	define('ecMailboxDisabled', 0x0000096C);
}
if (!defined('ecRulesFolderOverQuota')) {
	define('ecRulesFolderOverQuota', 0x0000096D);
}
if (!defined('ecADUnavailable')) {
	define('ecADUnavailable', 0x0000096E);
}
if (!defined('ecADError')) {
	define('ecADError', 0x0000096F);
}
if (!defined('ecNotEncrypted')) {
	define('ecNotEncrypted', 0x00000970);
}
if (!defined('ecADNotFound')) {
	define('ecADNotFound', 0x00000971);
}
if (!defined('ecADPropertyError')) {
	define('ecADPropertyError', 0x00000972);
}
if (!defined('ecRpcServerTooBusy')) {
	define('ecRpcServerTooBusy', 0x00000973);
}
if (!defined('ecRpcOutOfMemory')) {
	define('ecRpcOutOfMemory', 0x00000974);
}
if (!defined('ecRpcServerOutOfMemory')) {
	define('ecRpcServerOutOfMemory', 0x00000975);
}
if (!defined('ecRpcOutOfResources')) {
	define('ecRpcOutOfResources', 0x00000976);
}
if (!defined('ecRpcServerUnavailable')) {
	define('ecRpcServerUnavailable', 0x00000977);
}
if (!defined('ecSecureSubmitError')) {
	define('ecSecureSubmitError', 0x0000097A);
}
if (!defined('ecEventsDeleted')) {
	define('ecEventsDeleted', 0x0000097C);
}
if (!defined('ecSubsystemStopping')) {
	define('ecSubsystemStopping', 0x0000097D);
}
if (!defined('ecSAUnavailable')) {
	define('ecSAUnavailable', 0x0000097E);
}
if (!defined('ecCIStopping')) {
	define('ecCIStopping', 0x00000A28);
}
if (!defined('ecFxInvalidState')) {
	define('ecFxInvalidState', 0x00000A29);
}
if (!defined('ecFxUnexpectedMarker')) {
	define('ecFxUnexpectedMarker', 0x00000A2A);
}
if (!defined('ecDuplicateDelivery')) {
	define('ecDuplicateDelivery', 0x00000A2B);
}
if (!defined('ecConditionViolation')) {
	define('ecConditionViolation', 0x00000A2C);
}
if (!defined('ecMaxPoolExceeded')) {
	define('ecMaxPoolExceeded', 0x00000A2D);
}
if (!defined('ecRpcInvalidHandle')) {
	define('ecRpcInvalidHandle', 0x00000A2E);
}
if (!defined('ecEventNotFound')) {
	define('ecEventNotFound', 0x00000A2F);
}
if (!defined('ecPropNotPromoted')) {
	define('ecPropNotPromoted', 0x00000A30);
}
if (!defined('ecLowMdbSpace')) {
	define('ecLowMdbSpace', 0x00000A31);
}
if (!defined('MAPI_W_NO_SERVICE')) {
	define('MAPI_W_NO_SERVICE', 0x00040203);
}
if (!defined('MAPI_W_ERRORS_RETURNED')) {
	define('MAPI_W_ERRORS_RETURNED', 0x00040380);
}
if (!defined('MAPI_W_POSITION_CHANGED')) {
	define('MAPI_W_POSITION_CHANGED', 0x00040481);
}
if (!defined('MAPI_W_APPROX_COUNT')) {
	define('MAPI_W_APPROX_COUNT', 0x00040482);
}
if (!defined('MAPI_W_CANCEL_MESSAGE')) {
	define('MAPI_W_CANCEL_MESSAGE', 0x00040580);
}
if (!defined('MAPI_W_PARTIAL_COMPLETION')) {
	define('MAPI_W_PARTIAL_COMPLETION', 0x00040680);
}
if (!defined('SYNC_W_PROGRESS')) {
	define('SYNC_W_PROGRESS', 0x00040820);
}
if (!defined('SYNC_W_CLIENT_CHANGE_NEWER')) {
	define('SYNC_W_CLIENT_CHANGE_NEWER', 0x00040821);
}
if (!defined('MAPI_E_INTERFACE_NOT_SUPPORTED')) {
	define('MAPI_E_INTERFACE_NOT_SUPPORTED', 0x80004002);
}
if (!defined('MAPI_E_CALL_FAILED')) {
	define('MAPI_E_CALL_FAILED', 0x80004005);
}
if (!defined('SYNC_E_ERROR')) {
	define('SYNC_E_ERROR', 0x80004005);
}
if (!defined('MAPI_E_NO_SUPPORT')) {
	define('MAPI_E_NO_SUPPORT', 0x80040102);
}
if (!defined('MAPI_E_BAD_CHARWIDTH')) {
	define('MAPI_E_BAD_CHARWIDTH', 0x80040103);
}
if (!defined('MAPI_E_STRING_TOO_LONG')) {
	define('MAPI_E_STRING_TOO_LONG', 0x80040105);
}
if (!defined('MAPI_E_UNKNOWN_FLAGS')) {
	define('MAPI_E_UNKNOWN_FLAGS', 0x80040106);
}
if (!defined('SYNC_E_UNKNOWN_FLAGS')) {
	define('SYNC_E_UNKNOWN_FLAGS', 0x80040106);
}
if (!defined('MAPI_E_INVALID_ENTRYID')) {
	define('MAPI_E_INVALID_ENTRYID', 0x80040107);
}
if (!defined('MAPI_E_INVALID_OBJECT')) {
	define('MAPI_E_INVALID_OBJECT', 0x80040108);
}
if (!defined('MAPI_E_OBJECT_CHANGED')) {
	define('MAPI_E_OBJECT_CHANGED', 0x80040109);
}
if (!defined('MAPI_E_OBJECT_DELETED')) {
	define('MAPI_E_OBJECT_DELETED', 0x8004010A);
}
if (!defined('MAPI_E_BUSY')) {
	define('MAPI_E_BUSY', 0x8004010B);
}
if (!defined('MAPI_E_NOT_ENOUGH_DISK')) {
	define('MAPI_E_NOT_ENOUGH_DISK', 0x8004010D);
}
if (!defined('MAPI_E_NOT_ENOUGH_RESOURCES')) {
	define('MAPI_E_NOT_ENOUGH_RESOURCES', 0x8004010E);
}
if (!defined('MAPI_E_NOT_FOUND')) {
	define('MAPI_E_NOT_FOUND', 0x8004010F);
}
if (!defined('MAPI_E_VERSION')) {
	define('MAPI_E_VERSION', 0x80040110);
}
if (!defined('MAPI_E_LOGON_FAILED')) {
	define('MAPI_E_LOGON_FAILED', 0x80040111);
}
if (!defined('MAPI_E_SESSION_LIMIT')) {
	define('MAPI_E_SESSION_LIMIT', 0x80040112);
}
if (!defined('MAPI_E_USER_CANCEL')) {
	define('MAPI_E_USER_CANCEL', 0x80040113);
}
if (!defined('MAPI_E_UNABLE_TO_ABORT')) {
	define('MAPI_E_UNABLE_TO_ABORT', 0x80040114);
}
if (!defined('ecRpcFailed')) {
	define('ecRpcFailed', 0x80040115);
}
if (!defined('MAPI_E_NETWORK_ERROR')) {
	define('MAPI_E_NETWORK_ERROR', 0x80040115);
}
if (!defined('MAPI_E_DISK_ERROR')) {
	define('MAPI_E_DISK_ERROR', 0x80040116);
}
if (!defined('ecWriteFault')) {
	define('ecWriteFault', 0x80040116);
}
if (!defined('MAPI_E_TOO_COMPLEX')) {
	define('MAPI_E_TOO_COMPLEX', 0x80040117);
}
if (!defined('MAPI_E_BAD_COLUMN')) {
	define('MAPI_E_BAD_COLUMN', 0x80040118);
}
if (!defined('MAPI_E_EXTENDED_ERROR')) {
	define('MAPI_E_EXTENDED_ERROR', 0x80040119);
}
if (!defined('MAPI_E_COMPUTED')) {
	define('MAPI_E_COMPUTED', 0x8004011A);
}
if (!defined('MAPI_E_CORRUPT_DATA')) {
	define('MAPI_E_CORRUPT_DATA', 0x8004011B);
}
if (!defined('MAPI_E_UNCONFIGURED')) {
	define('MAPI_E_UNCONFIGURED', 0x8004011C);
}
if (!defined('MAPI_E_FAILONEPROVIDER')) {
	define('MAPI_E_FAILONEPROVIDER', 0x8004011D);
}
if (!defined('MAPI_E_UNKNOWN_CPID')) {
	define('MAPI_E_UNKNOWN_CPID', 0x8004011E);
}
if (!defined('MAPI_E_UNKNOWN_LCID')) {
	define('MAPI_E_UNKNOWN_LCID', 0x8004011F);
}
if (!defined('MAPI_E_PASSWORD_CHANGE_REQUIRED')) {
	define('MAPI_E_PASSWORD_CHANGE_REQUIRED', 0x80040120);
}
if (!defined('MAPI_E_PASSWORD_EXPIRED')) {
	define('MAPI_E_PASSWORD_EXPIRED', 0x80040121);
}
if (!defined('MAPI_E_INVALID_WORKSTATION_ACCOUNT')) {
	define('MAPI_E_INVALID_WORKSTATION_ACCOUNT', 0x80040122);
}
if (!defined('MAPI_E_INVALID_ACCESS_TIME')) {
	define('MAPI_E_INVALID_ACCESS_TIME', 0x80040123);
}
if (!defined('MAPI_E_ACCOUNT_DISABLED')) {
	define('MAPI_E_ACCOUNT_DISABLED', 0x80040124);
}
if (!defined('MAPI_E_END_OF_SESSION')) {
	define('MAPI_E_END_OF_SESSION', 0x80040200);
}
if (!defined('MAPI_E_UNKNOWN_ENTRYID')) {
	define('MAPI_E_UNKNOWN_ENTRYID', 0x80040201);
}
if (!defined('MAPI_E_MISSING_REQUIRED_COLUMN')) {
	define('MAPI_E_MISSING_REQUIRED_COLUMN', 0x80040202);
}
if (!defined('MAPI_E_BAD_VALUE')) {
	define('MAPI_E_BAD_VALUE', 0x80040301);
}
if (!defined('MAPI_E_INVALID_TYPE')) {
	define('MAPI_E_INVALID_TYPE', 0x80040302);
}
if (!defined('MAPI_E_TYPE_NO_SUPPORT')) {
	define('MAPI_E_TYPE_NO_SUPPORT', 0x80040303);
}
if (!defined('MAPI_E_UNEXPECTED_TYPE')) {
	define('MAPI_E_UNEXPECTED_TYPE', 0x80040304);
}
if (!defined('MAPI_E_TOO_BIG')) {
	define('MAPI_E_TOO_BIG', 0x80040305);
}
if (!defined('MAPI_E_DECLINE_COPY')) {
	define('MAPI_E_DECLINE_COPY', 0x80040306);
}
if (!defined('MAPI_E_UNEXPECTED_ID')) {
	define('MAPI_E_UNEXPECTED_ID', 0x80040307);
}
if (!defined('MAPI_E_UNABLE_TO_COMPLETE')) {
	define('MAPI_E_UNABLE_TO_COMPLETE', 0x80040400);
}
if (!defined('MAPI_E_TIMEOUT')) {
	define('MAPI_E_TIMEOUT', 0x80040401);
}
if (!defined('MAPI_E_TABLE_EMPTY')) {
	define('MAPI_E_TABLE_EMPTY', 0x80040402);
}
if (!defined('MAPI_E_TABLE_TOO_BIG')) {
	define('MAPI_E_TABLE_TOO_BIG', 0x80040403);
}
if (!defined('MAPI_E_INVALID_BOOKMARK')) {
	define('MAPI_E_INVALID_BOOKMARK', 0x80040405);
}
if (!defined('MAPI_E_WAIT')) {
	define('MAPI_E_WAIT', 0x80040500);
}
if (!defined('MAPI_E_CANCEL')) {
	define('MAPI_E_CANCEL', 0x80040501);
}
if (!defined('MAPI_E_NOT_ME')) {
	define('MAPI_E_NOT_ME', 0x80040502);
}
if (!defined('MAPI_E_CORRUPT_STORE')) {
	define('MAPI_E_CORRUPT_STORE', 0x80040600);
}
if (!defined('MAPI_E_NOT_IN_QUEUE')) {
	define('MAPI_E_NOT_IN_QUEUE', 0x80040601);
}
if (!defined('MAPI_E_NO_SUPPRESS')) {
	define('MAPI_E_NO_SUPPRESS', 0x80040602);
}
if (!defined('MAPI_E_COLLISION')) {
	define('MAPI_E_COLLISION', 0x80040604);
}
if (!defined('MAPI_E_NOT_INITIALIZED')) {
	define('MAPI_E_NOT_INITIALIZED', 0x80040605);
}
if (!defined('MAPI_E_NON_STANDARD')) {
	define('MAPI_E_NON_STANDARD', 0x80040606);
}
if (!defined('MAPI_E_NO_RECIPIENTS')) {
	define('MAPI_E_NO_RECIPIENTS', 0x80040607);
}
if (!defined('MAPI_E_SUBMITTED')) {
	define('MAPI_E_SUBMITTED', 0x80040608);
}
if (!defined('MAPI_E_HAS_FOLDERS')) {
	define('MAPI_E_HAS_FOLDERS', 0x80040609);
}
if (!defined('MAPI_E_HAS_MESSAGES')) {
	define('MAPI_E_HAS_MESSAGES', 0x8004060A);
}
if (!defined('MAPI_E_FOLDER_CYCLE')) {
	define('MAPI_E_FOLDER_CYCLE', 0x8004060B);
}
if (!defined('MAPI_E_STORE_FULL')) {
	define('MAPI_E_STORE_FULL', 0x8004060C);
}
if (!defined('MAPI_E_LOCKID_LIMIT')) {
	define('MAPI_E_LOCKID_LIMIT', 0x8004060D);
}
if (!defined('MAPI_E_AMBIGUOUS_RECIP')) {
	define('MAPI_E_AMBIGUOUS_RECIP', 0x80040700);
}
if (!defined('SYNC_E_OBJECT_DELETED')) {
	define('SYNC_E_OBJECT_DELETED', 0x80040800);
}
if (!defined('SYNC_E_IGNORE')) {
	define('SYNC_E_IGNORE', 0x80040801);
}
if (!defined('SYNC_E_CONFLICT')) {
	define('SYNC_E_CONFLICT', 0x80040802);
}
if (!defined('SYNC_E_NO_PARENT')) {
	define('SYNC_E_NO_PARENT', 0x80040803);
}
if (!defined('SYNC_E_CYCLE_DETECTED')) {
	define('SYNC_E_CYCLE_DETECTED', 0x80040804);
}
if (!defined('SYNC_E_CYCLE')) {
	define('SYNC_E_CYCLE', 0x80040804);
}
if (!defined('SYNC_E_INCEST')) {
	define('SYNC_E_INCEST', 0x80040804);
}
if (!defined('SYNC_E_UNSYNCHRONIZED')) {
	define('SYNC_E_UNSYNCHRONIZED', 0x80040805);
}
if (!defined('MAPI_E_NAMED_PROP_QUOTA_EXCEEDED')) {
	define('MAPI_E_NAMED_PROP_QUOTA_EXCEEDED', 0x80040900);
}
if (!defined('MAPI_E_NO_ACCESS')) {
	define('MAPI_E_NO_ACCESS', 0x80070005);
}
if (!defined('MAPI_E_NOT_ENOUGH_MEMORY')) {
	define('MAPI_E_NOT_ENOUGH_MEMORY', 0x8007000E);
}
if (!defined('MAPI_E_INVALID_PARAMETER')) {
	define('MAPI_E_INVALID_PARAMETER', 0x80070057);
}
if (!defined('SYNC_E_INVALID_PARAMETER')) {
	define('SYNC_E_INVALID_PARAMETER', 0x80070057);
}
if (!defined('ecZNullObject')) {
	define('ecZNullObject', 0xFFFFFC00);
}
if (!defined('ecZOutOfHandles')) {
	define('ecZOutOfHandles', 0xFFFFFC04);
}
?>