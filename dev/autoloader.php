<?php

class Resource {}

/**
 * @param ?int $level
 */
function mapi_load_mapidefs(?int $level): void {}

function mapi_last_hresult(): int {
	return 0;
}

function mapi_prop_type(int $proptag): false|int {
	return 0;
}

function mapi_prop_id(int $proptag): false|int {
	return 0;
}

function mapi_is_error(int $errcode): bool|false {
	return false;
}

function mapi_make_scode(int $sev, int $code): false|int {
	return 0;
}

function mapi_prop_tag(int $proptype, int $propid): false|int {
	return 0;
}

/**
 * @param ?string $displayname
 * @param ?int    $flags
 */
function mapi_createoneoff(?string $displayname, string $type, string $address, ?int $flags = 0): false|string {
	return '';
}

function mapi_parseoneoff(string $entryid): array|false {
	return [];
}

/**
 * @param ?string $server
 * @param ?string $sslcert
 * @param ?string $sslpass
 * @param ?int    $flags
 * @param ?string $wa_version
 * @param ?string $misc_version
 */
function mapi_logon_zarafa(string $username, string $password, ?string $server = null, ?string $sslcert = null, ?string $sslpass = null, ?int $flags = 0, ?string $wa_version = null, ?string $misc_version = null): false|Resource {
	return null;
}

function mapi_logon_ex(string $username, string $password, int $flags): false|Resource {
	return null;
}

function mapi_logon_token(string $token): false|Resource {
	return null;
}

function mapi_getmsgstorestable(Resource $session): false|Resource {
	return null;
}

function mapi_openmsgstore(Resource $ses, string $entryid): false|Resource {
	return null;
}

function mapi_openprofilesection(Resource $ses, string $uid): false|Resource {
	return null;
}

function mapi_openaddressbook(Resource $session): false|Resource {
	return null;
}

/**
 * @param ?string $entryid
 * @param ?int    $flags
 */
function mapi_openentry(Resource $ses, ?string $entryid = null, ?int $flags = 0): false|Resource {
	return null;
}

/**
 * @param ?string $entryid
 * @param ?int    $flags
 */
function mapi_ab_openentry(Resource $abk, ?string $entryid = null, ?int $flags = 0): false|Resource {
	return null;
}

/**
 * @param ?int $flags
 */
function mapi_ab_resolvename(Resource $abk, array $names, ?int $flags = 0): mixed {
	return null;
}

function mapi_ab_getdefaultdir(Resource $abk): false|string {
	return '';
}

function mapi_msgstore_createentryid(Resource $store, string $mailbox_dn): false|string {
	return '';
}

function mapi_msgstore_getarchiveentryid(Resource $store, string $user, string $server): bool {
	return false;
}

/**
 * @param ?string $entryid
 * @param ?int    $flags
 */
function mapi_msgstore_openentry(Resource $store, ?string $entryid = null, ?int $flags = 0): false|Resource {
	return null;
}

function mapi_msgstore_getreceivefolder(Resource $store): false|Resource {
	return null;
}

/**
 * @param ?string $sk_msg
 */
function mapi_msgstore_entryidfromsourcekey(Resource $store, string $sk_fld, ?string $sk_msg = null): false|string {
	return '';
}

function mapi_msgstore_advise(Resource $store, string $entryid, int $event_mask, Resource $sink): false|int {
	return 0;
}

function mapi_msgstore_unadvise(Resource $store, int $sub_id): bool {
	return false;
}

/**
 * @param ?Resource $store
 * @param ?string   $entryid
 */
function mapi_msgstore_abortsubmit(?Resource $store, ?string $entryid = null): true {
	return null;
}

function mapi_sink_create(): false|Resource {
	return null;
}

function mapi_sink_timedwait(Resource $sink, int $time): mixed {
	return null;
}

/**
 * @param ?array $proptags
 * @param ?array $restrict
 */
function mapi_table_queryallrows(Resource $table, ?array $proptags = null, ?array $restrict = null): mixed {
	return null;
}

/**
 * @param ?array $proptags
 * @param ?int   $start
 * @param ?int   $limit
 */
function mapi_table_queryrows(Resource $table, ?array $proptags = null, ?int $start = 0, ?int $limit = 0): mixed {
	return null;
}

function mapi_table_getrowcount(Resource $table): false|int {
	return 0;
}

/**
 * @param ?int $flags
 */
function mapi_table_setcolumns(Resource $table, array $columns, ?int $flags = 0): bool {
	return false;
}

function mapi_table_seekrow(Resource $table, int $bookmark, int $rowcount): false|int {
	return 0;
}

/**
 * @param ?int $flags
 */
function mapi_table_sort(Resource $table, array $sortcrit, ?int $flags = 0): bool {
	return false;
}

/**
 * @param ?int $flags
 */
function mapi_table_restrict(Resource $table, array $restrict, ?int $flags = 0): bool {
	return false;
}

/**
 * @param ?int $bookmark
 * @param ?int $flags
 */
function mapi_table_findrow(Resource $table, array $restrict, ?int $bookmark = 0, ?int $flags = 0): false|int {
	return 0;
}

function mapi_table_createbookmark(Resource $table): false|int {
	return 0;
}

function mapi_table_freebookmark(Resource $table, int $bookmark): bool {
	return false;
}

/**
 * @param ?int $flags
 */
function mapi_folder_gethierarchytable(Resource $fld, ?int $flags = 0): false|Resource {
	return null;
}

/**
 * @param ?int $flags
 */
function mapi_folder_getcontentstable(Resource $fld, ?int $flags = 0): false|Resource {
	return null;
}

function mapi_folder_getrulestable(Resource $fld): false|Resource {
	return null;
}

/**
 * @param ?int $flags
 */
function mapi_folder_createmessage(Resource $fld, ?int $flags = 0): false|Resource {
	return null;
}

/**
 * @param ?string $comment
 * @param ?int    $flags
 * @param ?int    $folder_type
 */
function mapi_folder_createfolder(Resource $fld, string $fname, ?string $comment = null, ?int $flags = 0, ?int $folder_type = 0): false|Resource {
	return null;
}

/**
 * @param ?int $flags
 */
function mapi_folder_deletemessages(Resource $fld, array $entryids, ?int $flags = 0): bool {
	return false;
}

/**
 * @param ?int $flags
 */
function mapi_folder_copymessages(Resource $srcfld, array $entryids, Resource $dstfld, ?int $flags = 0): bool {
	return false;
}

/**
 * @param ?int $flags
 */
function mapi_folder_emptyfolder(Resource $fld, ?int $flags = 0): bool {
	return false;
}

/**
 * @param ?string $name
 * @param ?int    $flags
 */
function mapi_folder_copyfolder(Resource $srcfld, string $entryid, Resource $dstfld, ?string $name, ?int $flags = 0): bool {
	return false;
}

/**
 * @param ?int $flags
 */
function mapi_folder_deletefolder(Resource $fld, string $entryid, ?int $flags = 0): bool {
	return false;
}

/**
 * @param ?int $flags
 */
function mapi_folder_setreadflags(Resource $fld, array $entryids, ?int $flags = 0): bool {
	return false;
}

function mapi_folder_setsearchcriteria(Resource $fld, array $restriction, array $folderlist, int $flags): bool {
	return false;
}

/**
 * @param ?int $flags
 */
function mapi_folder_getsearchcriteria(Resource $fld, ?int $flags = 0): mixed {
	return null;
}

/**
 * @param ?int $flags
 */
function mapi_folder_modifyrules(Resource $fld, array $rows, ?int $flags = 0): bool {
	return false;
}

function mapi_message_getattachmenttable(Resource $msg): false|Resource {
	return null;
}

function mapi_message_getrecipienttable(Resource $msg): false|Resource {
	return null;
}

function mapi_message_openattach(Resource $msg, int $id): false|Resource {
	return null;
}

/**
 * @param ?int $flags
 */
function mapi_message_createattach(Resource $msg, ?int $flags = 0): false|Resource {
	return null;
}

/**
 * @param ?int $flags
 */
function mapi_message_deleteattach(Resource $msg, int $id = 0, ?int $flags = 0): bool {
	return false;
}

function mapi_message_modifyrecipients(Resource $msg, int $flags, array $adrlist): bool {
	return false;
}

function mapi_message_submitmessage(Resource $msg): bool {
	return false;
}

function mapi_message_setreadflag(Resource $msg, int $flags): bool {
	return false;
}

/**
 * @param ?int    $flags
 * @param ?string $guid
 */
function mapi_openpropertytostream(Resource $any, int $proptag, ?int $flags = 0, ?string $guid = null): false|Resource {
	return null;
}

function mapi_stream_write(Resource $stream, string $data): false|int {
	return 0;
}

function mapi_stream_read(Resource $stream, int $size): false|string {
	return '';
}

function mapi_stream_stat(Resource $stream): array|false {
	return [];
}

/**
 * @param ?int $flags
 */
function mapi_stream_seek(Resource $stream, int $offset, ?int $flags = 0): bool {
	return false;
}

function mapi_stream_commit(Resource $stream): bool {
	return false;
}

function mapi_stream_setsize(Resource $stream, int $size): bool {
	return false;
}

function mapi_stream_create(): false|Resource {
	return null;
}

/**
 * @param ?int $flags
 */
function mapi_attach_openobj(Resource $attach, ?int $flags = 0): bool|Resource {
	return null;
}

/**
 * @param ?int $flags
 */
function mapi_savechanges(Resource $any, ?int $flags = 0): bool {
	return false;
}

/**
 * @param ?array $proptags
 */
function mapi_getprops(Resource $any, ?array $proptags = null): mixed {
	return null;
}

function mapi_setprops(Resource $any, array $propvals): bool {
	return false;
}

/**
 * @param ?int $flags
 */
function mapi_copyto(Resource $src, array $excliid, array $exclprop, Resource $dst, ?int $flags = 0): bool {
	return false;
}

function mapi_openproperty(Resource $any, int $proptag /* [more] */): false|Resource {
	return null;
}

function mapi_deleteprops(Resource $any, array $proptags): bool {
	return false;
}

/**
 * @param ?array $names
 */
function mapi_getnamesfromids(Resource $any, ?array $names = null): array|false {
	return [];
}

/**
 * @param ?array $guids
 */
function mapi_getidsfromnames(Resource $store, array $names, ?array $guids = null): array|false {
	return [];
}

function mapi_decompressrtf(string $data): false|string {
	return '';
}

function mapi_zarafa_getpermissionrules(Resource $any, int $type): array|false {
	return [];
}

function mapi_zarafa_setpermissionrules(Resource $any, array $perms): bool {
	return false;
}

function mapi_getuserfreebusy(Resource $ses, string $entryid, int $start, int $end): array|false {
	return [];
}

function mapi_getuserfreebusyical(Resource $ses, string $entryid, int $start, int $end): false|string {
	return '';
}

function mapi_exportchanges_config(Resource $e, Resource $stream, int $flags, mixed $i, mixed $restrict, mixed $inclprop, mixed $exclprop, int $bufsize): bool {
	return false;
}

function mapi_exportchanges_synchronize(Resource $x): mixed {
	return null;
}

function mapi_exportchanges_updatestate(Resource $e, Resource $stream): bool {
	return false;
}

function mapi_exportchanges_getchangecount(Resource $r): false|int {
	return 0;
}

function mapi_importcontentschanges_config(Resource $i, Resource $stream, int $flags): bool {
	return false;
}

/**
 * @param ?Resource $stream
 */
function mapi_importcontentschanges_updatestate(Resource $i, ?Resource $stream = null): bool {
	return false;
}

function mapi_importcontentschanges_importmessagechange(Resource $i, array $props, int $flags, mixed &$msg): bool {
	return false;
}

function mapi_importcontentschanges_importmessagedeletion(Resource $i, int $flags, array $msgs): bool {
	return false;
}

function mapi_importcontentschanges_importperuserreadstatechange(Resource $i, array $readst): bool {
	return false;
}

function mapi_importcontentschanges_importmessagemove(Resource $r, string $a, string $b, string $c, string $d, string $e): bool {
	return false;
}

function mapi_importhierarchychanges_config(Resource $i, Resource $stream, int $flags): bool {
	return false;
}

/**
 * @param ?Resource $stream
 */
function mapi_importhierarchychanges_updatestate(Resource $i, ?Resource $stream): bool {
	return false;
}

function mapi_importhierarchychanges_importfolderchange(Resource $i, array $props): bool {
	return false;
}

function mapi_importhierarchychanges_importfolderdeletion(Resource $i, int $flags, array $folders): bool {
	return false;
}

function mapi_wrap_importcontentschanges(object &$object): false|Resource {
	return null;
}

function mapi_wrap_importhierarchychanges(object &$object): false|Resource {
	return null;
}

function mapi_inetmapi_imtoinet(Resource $ses, Resource $abk, Resource $msg, array $opts): false|Resource {
	return null;
}

function mapi_inetmapi_imtomapi(Resource $ses, Resource $store, Resource $abk, Resource $msg, string $str, array $opts): bool {
	return false;
}

function mapi_icaltomapi(Resource $ses, Resource $store, Resource $abk, Resource $msg, string $str, bool $norecip): bool {
	return false;
}

function mapi_icaltomapi2(Resource $abk, Resource $fld, string $ics): array|false {
	return [];
}

function mapi_mapitoical(Resource $ses, Resource $abk, Resource $msg, array $opts): false|string {
	return '';
}

function mapi_vcftomapi(Resource $ses, Resource $store, Resource $msg, string $str): bool {
	return false;
}

function mapi_vcftomapi2(Resource $fld, string $vcard): array|false {
	return [];
}

function mapi_mapitovcf(Resource $ses, Resource $abk, Resource $msg, array $opts): false|string {
	return '';
}

function mapi_enable_exceptions(string $cls): bool {
	return false;
}

function mapi_feature(string $ft): bool {
	return false;
}

function kc_session_save(Resource $ses, string &$data): int {
	return 0;
}

function kc_session_restore(mixed $data, mixed &$res): int {
	return 0;
}

function nsp_getuserinfo(string $username): array|false {
	return [];
}

function nsp_setuserpasswd(string $username, string $oldpass, string $newpass): bool {
	return false;
}

function nsp_essdn_to_username(string $essdn): false|string {
	return '';
}

/**
 * @param ?string $srcheid
 * @param ?string $msgeid
 */
function mapi_linkmessage(Resource $ses, ?string $srcheid = null, ?string $msgeid = null): mixed {
	return null;
}

function mapi_ianatz_to_tzdef(string $tz): false|string {
	return '';
}

function mapi_strerror(int $code): string {
	return '';
}

if (!defined('MAPIDEFS_LOADED')) {
	define('MAPIDEFS_LOADED', 1);
}
if (!defined('PR_NULL')) {
	define('PR_NULL', 0);
}
if (!defined('PR_EMS_TEMPLATE_BLOB')) {
	define('PR_EMS_TEMPLATE_BLOB', 65794);
}
if (!defined('PR_ACKNOWLEDGEMENT_MODE')) {
	define('PR_ACKNOWLEDGEMENT_MODE', 65539);
}
if (!defined('PR_ALTERNATE_RECIPIENT_ALLOWED')) {
	define('PR_ALTERNATE_RECIPIENT_ALLOWED', 131330);
}
if (!defined('PR_AUTHORIZING_USERS')) {
	define('PR_AUTHORIZING_USERS', 196866);
}
if (!defined('PR_AUTO_FORWARD_COMMENT')) {
	define('PR_AUTO_FORWARD_COMMENT', 262174);
}
if (!defined('PR_EMS_SCRIPT_BLOB')) {
	define('PR_EMS_SCRIPT_BLOB', 262402);
}
if (!defined('PR_AUTO_FORWARDED')) {
	define('PR_AUTO_FORWARDED', 327691);
}
if (!defined('PR_CONTENT_CONFIDENTIALITY_ALGORITHM_ID')) {
	define('PR_CONTENT_CONFIDENTIALITY_ALGORITHM_ID', 393474);
}
if (!defined('PR_CONTENT_CORRELATOR')) {
	define('PR_CONTENT_CORRELATOR', 459010);
}
if (!defined('PR_CONTENT_IDENTIFIER')) {
	define('PR_CONTENT_IDENTIFIER', 524318);
}
if (!defined('PR_CONTENT_LENGTH')) {
	define('PR_CONTENT_LENGTH', 589827);
}
if (!defined('PR_CONTENT_RETURN_REQUESTED')) {
	define('PR_CONTENT_RETURN_REQUESTED', 655371);
}
if (!defined('PR_CONVERSATION_KEY')) {
	define('PR_CONVERSATION_KEY', 721154);
}
if (!defined('PR_CONVERSION_EITS')) {
	define('PR_CONVERSION_EITS', 786690);
}
if (!defined('PR_CONVERSION_WITH_LOSS_PROHIBITED')) {
	define('PR_CONVERSION_WITH_LOSS_PROHIBITED', 851979);
}
if (!defined('PR_CONVERTED_EITS')) {
	define('PR_CONVERTED_EITS', 917762);
}
if (!defined('PR_DEFERRED_DELIVERY_TIME')) {
	define('PR_DEFERRED_DELIVERY_TIME', 983104);
}
if (!defined('PR_DELIVER_TIME')) {
	define('PR_DELIVER_TIME', 1048640);
}
if (!defined('PR_DISCARD_REASON')) {
	define('PR_DISCARD_REASON', 1114115);
}
if (!defined('PR_DISCLOSURE_OF_RECIPIENTS')) {
	define('PR_DISCLOSURE_OF_RECIPIENTS', 1179659);
}
if (!defined('PR_DL_EXPANSION_HISTORY')) {
	define('PR_DL_EXPANSION_HISTORY', 1245442);
}
if (!defined('PR_DL_EXPANSION_PROHIBITED')) {
	define('PR_DL_EXPANSION_PROHIBITED', 1310731);
}
if (!defined('PR_EXPIRY_TIME')) {
	define('PR_EXPIRY_TIME', 1376320);
}
if (!defined('PR_IMPLICIT_CONVERSION_PROHIBITED')) {
	define('PR_IMPLICIT_CONVERSION_PROHIBITED', 1441803);
}
if (!defined('PR_IMPORTANCE')) {
	define('PR_IMPORTANCE', 1507331);
}
if (!defined('PR_IPM_ID')) {
	define('PR_IPM_ID', 1573122);
}
if (!defined('PR_LATEST_DELIVERY_TIME')) {
	define('PR_LATEST_DELIVERY_TIME', 1638464);
}
if (!defined('PR_MESSAGE_CLASS')) {
	define('PR_MESSAGE_CLASS', 1703966);
}
if (!defined('PR_MESSAGE_CLASS_A')) {
	define('PR_MESSAGE_CLASS_A', 1703966);
}
if (!defined('PR_MESSAGE_DELIVERY_ID')) {
	define('PR_MESSAGE_DELIVERY_ID', 1769730);
}
if (!defined('PR_MESSAGE_SECURITY_LABEL')) {
	define('PR_MESSAGE_SECURITY_LABEL', 1966338);
}
if (!defined('PR_OBSOLETED_IPMS')) {
	define('PR_OBSOLETED_IPMS', 2031874);
}
if (!defined('PR_ORIGINALLY_INTENDED_RECIPIENT_NAME')) {
	define('PR_ORIGINALLY_INTENDED_RECIPIENT_NAME', 2097410);
}
if (!defined('PR_ORIGINAL_EITS')) {
	define('PR_ORIGINAL_EITS', 2162946);
}
if (!defined('PR_ORIGINATOR_CERTIFICATE')) {
	define('PR_ORIGINATOR_CERTIFICATE', 2228482);
}
if (!defined('PR_ORIGINATOR_DELIVERY_REPORT_REQUESTED')) {
	define('PR_ORIGINATOR_DELIVERY_REPORT_REQUESTED', 2293771);
}
if (!defined('PR_ORIGINATOR_RETURN_ADDRESS')) {
	define('PR_ORIGINATOR_RETURN_ADDRESS', 2359554);
}
if (!defined('PR_PARENT_KEY')) {
	define('PR_PARENT_KEY', 2425090);
}
if (!defined('PR_PRIORITY')) {
	define('PR_PRIORITY', 2490371);
}
if (!defined('PR_ORIGIN_CHECK')) {
	define('PR_ORIGIN_CHECK', 2556162);
}
if (!defined('PR_PROOF_OF_SUBMISSION_REQUESTED')) {
	define('PR_PROOF_OF_SUBMISSION_REQUESTED', 2621451);
}
if (!defined('PR_READ_RECEIPT_REQUESTED')) {
	define('PR_READ_RECEIPT_REQUESTED', 2686987);
}
if (!defined('PR_RECEIPT_TIME')) {
	define('PR_RECEIPT_TIME', 2752576);
}
if (!defined('PR_RECIPIENT_REASSIGNMENT_PROHIBITED')) {
	define('PR_RECIPIENT_REASSIGNMENT_PROHIBITED', 2818059);
}
if (!defined('PR_REDIRECTION_HISTORY')) {
	define('PR_REDIRECTION_HISTORY', 2883842);
}
if (!defined('PR_RELATED_IPMS')) {
	define('PR_RELATED_IPMS', 2949378);
}
if (!defined('PR_ORIGINAL_SENSITIVITY')) {
	define('PR_ORIGINAL_SENSITIVITY', 3014659);
}
if (!defined('PR_LANGUAGES')) {
	define('PR_LANGUAGES', 3080222);
}
if (!defined('PR_REPLY_TIME')) {
	define('PR_REPLY_TIME', 3145792);
}
if (!defined('PR_REPORT_TAG')) {
	define('PR_REPORT_TAG', 3211522);
}
if (!defined('PR_REPORT_TIME')) {
	define('PR_REPORT_TIME', 3276864);
}
if (!defined('PR_RETURNED_IPM')) {
	define('PR_RETURNED_IPM', 3342347);
}
if (!defined('PR_SECURITY')) {
	define('PR_SECURITY', 3407875);
}
if (!defined('PR_INCOMPLETE_COPY')) {
	define('PR_INCOMPLETE_COPY', 3473419);
}
if (!defined('PR_SENSITIVITY')) {
	define('PR_SENSITIVITY', 3538947);
}
if (!defined('PR_SUBJECT')) {
	define('PR_SUBJECT', 3604510);
}
if (!defined('PR_SUBJECT_A')) {
	define('PR_SUBJECT_A', 3604510);
}
if (!defined('PR_SUBJECT_IPM')) {
	define('PR_SUBJECT_IPM', 3670274);
}
if (!defined('PR_CLIENT_SUBMIT_TIME')) {
	define('PR_CLIENT_SUBMIT_TIME', 3735616);
}
if (!defined('PR_REPORT_NAME')) {
	define('PR_REPORT_NAME', 3801118);
}
if (!defined('PR_SENT_REPRESENTING_SEARCH_KEY')) {
	define('PR_SENT_REPRESENTING_SEARCH_KEY', 3866882);
}
if (!defined('PR_X400_CONTENT_TYPE')) {
	define('PR_X400_CONTENT_TYPE', 3932418);
}
if (!defined('PR_SUBJECT_PREFIX')) {
	define('PR_SUBJECT_PREFIX', 3997726);
}
if (!defined('PR_SUBJECT_PREFIX_A')) {
	define('PR_SUBJECT_PREFIX_A', 3997726);
}
if (!defined('PR_NON_RECEIPT_REASON')) {
	define('PR_NON_RECEIPT_REASON', 4063235);
}
if (!defined('PR_RECEIVED_BY_ENTRYID')) {
	define('PR_RECEIVED_BY_ENTRYID', 4129026);
}
if (!defined('PR_RECEIVED_BY_NAME')) {
	define('PR_RECEIVED_BY_NAME', 4194334);
}
if (!defined('PR_SENT_REPRESENTING_ENTRYID')) {
	define('PR_SENT_REPRESENTING_ENTRYID', 4260098);
}
if (!defined('PR_SENT_REPRESENTING_NAME')) {
	define('PR_SENT_REPRESENTING_NAME', 4325406);
}
if (!defined('PR_SENT_REPRESENTING_NAME_A')) {
	define('PR_SENT_REPRESENTING_NAME_A', 4325406);
}
if (!defined('PR_RCVD_REPRESENTING_ENTRYID')) {
	define('PR_RCVD_REPRESENTING_ENTRYID', 4391170);
}
if (!defined('PR_RCVD_REPRESENTING_NAME')) {
	define('PR_RCVD_REPRESENTING_NAME', 4456478);
}
if (!defined('PR_RCVD_REPRESENTING_NAME_A')) {
	define('PR_RCVD_REPRESENTING_NAME_A', 4456478);
}
if (!defined('PR_REPORT_ENTRYID')) {
	define('PR_REPORT_ENTRYID', 4522242);
}
if (!defined('PR_READ_RECEIPT_ENTRYID')) {
	define('PR_READ_RECEIPT_ENTRYID', 4587778);
}
if (!defined('PR_MESSAGE_SUBMISSION_ID')) {
	define('PR_MESSAGE_SUBMISSION_ID', 4653314);
}
if (!defined('PR_PROVIDER_SUBMIT_TIME')) {
	define('PR_PROVIDER_SUBMIT_TIME', 4718656);
}
if (!defined('PR_ORIGINAL_SUBJECT')) {
	define('PR_ORIGINAL_SUBJECT', 4784158);
}
if (!defined('PR_DISC_VAL')) {
	define('PR_DISC_VAL', 4849675);
}
if (!defined('PR_ORIG_MESSAGE_CLASS')) {
	define('PR_ORIG_MESSAGE_CLASS', 4915230);
}
if (!defined('PR_ORIG_MESSAGE_CLASS_A')) {
	define('PR_ORIG_MESSAGE_CLASS_A', 4915230);
}
if (!defined('PR_ORIGINAL_AUTHOR_ENTRYID')) {
	define('PR_ORIGINAL_AUTHOR_ENTRYID', 4980994);
}
if (!defined('PR_ORIGINAL_AUTHOR_NAME')) {
	define('PR_ORIGINAL_AUTHOR_NAME', 5046302);
}
if (!defined('PR_ORIGINAL_SUBMIT_TIME')) {
	define('PR_ORIGINAL_SUBMIT_TIME', 5111872);
}
if (!defined('PR_REPLY_RECIPIENT_ENTRIES')) {
	define('PR_REPLY_RECIPIENT_ENTRIES', 5177602);
}
if (!defined('PR_REPLY_RECIPIENT_NAMES')) {
	define('PR_REPLY_RECIPIENT_NAMES', 5242910);
}
if (!defined('PR_RECEIVED_BY_SEARCH_KEY')) {
	define('PR_RECEIVED_BY_SEARCH_KEY', 5308674);
}
if (!defined('PR_RCVD_REPRESENTING_SEARCH_KEY')) {
	define('PR_RCVD_REPRESENTING_SEARCH_KEY', 5374210);
}
if (!defined('PR_READ_RECEIPT_SEARCH_KEY')) {
	define('PR_READ_RECEIPT_SEARCH_KEY', 5439746);
}
if (!defined('PR_REPORT_SEARCH_KEY')) {
	define('PR_REPORT_SEARCH_KEY', 5505282);
}
if (!defined('PR_ORIGINAL_DELIVERY_TIME')) {
	define('PR_ORIGINAL_DELIVERY_TIME', 5570624);
}
if (!defined('PR_ORIGINAL_AUTHOR_SEARCH_KEY')) {
	define('PR_ORIGINAL_AUTHOR_SEARCH_KEY', 5636354);
}
if (!defined('PR_MESSAGE_TO_ME')) {
	define('PR_MESSAGE_TO_ME', 5701643);
}
if (!defined('PR_MESSAGE_CC_ME')) {
	define('PR_MESSAGE_CC_ME', 5767179);
}
if (!defined('PR_MESSAGE_RECIP_ME')) {
	define('PR_MESSAGE_RECIP_ME', 5832715);
}
if (!defined('PR_ORIGINAL_SENDER_NAME')) {
	define('PR_ORIGINAL_SENDER_NAME', 5898270);
}
if (!defined('PR_ORIGINAL_SENDER_ENTRYID')) {
	define('PR_ORIGINAL_SENDER_ENTRYID', 5964034);
}
if (!defined('PR_ORIGINAL_SENDER_SEARCH_KEY')) {
	define('PR_ORIGINAL_SENDER_SEARCH_KEY', 6029570);
}
if (!defined('PR_ORIGINAL_SENT_REPRESENTING_NAME')) {
	define('PR_ORIGINAL_SENT_REPRESENTING_NAME', 6094878);
}
if (!defined('PR_ORIGINAL_SENT_REPRESENTING_ENTRYID')) {
	define('PR_ORIGINAL_SENT_REPRESENTING_ENTRYID', 6160642);
}
if (!defined('PR_ORIGINAL_SENT_REPRESENTING_SEARCH_KEY')) {
	define('PR_ORIGINAL_SENT_REPRESENTING_SEARCH_KEY', 6226178);
}
if (!defined('PR_START_DATE')) {
	define('PR_START_DATE', 6291520);
}
if (!defined('PR_END_DATE')) {
	define('PR_END_DATE', 6357056);
}
if (!defined('PR_OWNER_APPT_ID')) {
	define('PR_OWNER_APPT_ID', 6422531);
}
if (!defined('PR_RESPONSE_REQUESTED')) {
	define('PR_RESPONSE_REQUESTED', 6488075);
}
if (!defined('PR_SENT_REPRESENTING_ADDRTYPE')) {
	define('PR_SENT_REPRESENTING_ADDRTYPE', 6553630);
}
if (!defined('PR_SENT_REPRESENTING_ADDRTYPE_A')) {
	define('PR_SENT_REPRESENTING_ADDRTYPE_A', 6553630);
}
if (!defined('PR_SENT_REPRESENTING_EMAIL_ADDRESS')) {
	define('PR_SENT_REPRESENTING_EMAIL_ADDRESS', 6619166);
}
if (!defined('PR_SENT_REPRESENTING_EMAIL_ADDRESS_A')) {
	define('PR_SENT_REPRESENTING_EMAIL_ADDRESS_A', 6619166);
}
if (!defined('PR_ORIGINAL_SENDER_ADDRTYPE')) {
	define('PR_ORIGINAL_SENDER_ADDRTYPE', 6684702);
}
if (!defined('PR_ORIGINAL_SENDER_EMAIL_ADDRESS')) {
	define('PR_ORIGINAL_SENDER_EMAIL_ADDRESS', 6750238);
}
if (!defined('PR_ORIGINAL_SENT_REPRESENTING_ADDRTYPE')) {
	define('PR_ORIGINAL_SENT_REPRESENTING_ADDRTYPE', 6815774);
}
if (!defined('PR_ORIGINAL_SENT_REPRESENTING_EMAIL_ADDRESS')) {
	define('PR_ORIGINAL_SENT_REPRESENTING_EMAIL_ADDRESS', 6881310);
}
if (!defined('PR_CONVERSATION_TOPIC')) {
	define('PR_CONVERSATION_TOPIC', 7340062);
}
if (!defined('PR_CONVERSATION_TOPIC_A')) {
	define('PR_CONVERSATION_TOPIC_A', 7340062);
}
if (!defined('PR_CONVERSATION_INDEX')) {
	define('PR_CONVERSATION_INDEX', 7405826);
}
if (!defined('PR_ORIGINAL_DISPLAY_BCC')) {
	define('PR_ORIGINAL_DISPLAY_BCC', 7471134);
}
if (!defined('PR_ORIGINAL_DISPLAY_CC')) {
	define('PR_ORIGINAL_DISPLAY_CC', 7536670);
}
if (!defined('PR_ORIGINAL_DISPLAY_TO')) {
	define('PR_ORIGINAL_DISPLAY_TO', 7602206);
}
if (!defined('PR_RECEIVED_BY_ADDRTYPE')) {
	define('PR_RECEIVED_BY_ADDRTYPE', 7667742);
}
if (!defined('PR_RECEIVED_BY_EMAIL_ADDRESS')) {
	define('PR_RECEIVED_BY_EMAIL_ADDRESS', 7733278);
}
if (!defined('PR_RCVD_REPRESENTING_ADDRTYPE')) {
	define('PR_RCVD_REPRESENTING_ADDRTYPE', 7798814);
}
if (!defined('PR_RCVD_REPRESENTING_ADDRTYPE_A')) {
	define('PR_RCVD_REPRESENTING_ADDRTYPE_A', 7798814);
}
if (!defined('PR_RCVD_REPRESENTING_EMAIL_ADDRESS')) {
	define('PR_RCVD_REPRESENTING_EMAIL_ADDRESS', 7864350);
}
if (!defined('PR_RCVD_REPRESENTING_EMAIL_ADDRESS_A')) {
	define('PR_RCVD_REPRESENTING_EMAIL_ADDRESS_A', 7864350);
}
if (!defined('PR_ORIGINAL_AUTHOR_ADDRTYPE')) {
	define('PR_ORIGINAL_AUTHOR_ADDRTYPE', 7929886);
}
if (!defined('PR_ORIGINAL_AUTHOR_EMAIL_ADDRESS')) {
	define('PR_ORIGINAL_AUTHOR_EMAIL_ADDRESS', 7995422);
}
if (!defined('PR_ORIGINALLY_INTENDED_RECIP_ADDRTYPE')) {
	define('PR_ORIGINALLY_INTENDED_RECIP_ADDRTYPE', 8060958);
}
if (!defined('PR_ORIGINALLY_INTENDED_RECIP_EMAIL_ADDRESS')) {
	define('PR_ORIGINALLY_INTENDED_RECIP_EMAIL_ADDRESS', 8126494);
}
if (!defined('PR_TRANSPORT_MESSAGE_HEADERS')) {
	define('PR_TRANSPORT_MESSAGE_HEADERS', 8192030);
}
if (!defined('PR_TRANSPORT_MESSAGE_HEADERS_A')) {
	define('PR_TRANSPORT_MESSAGE_HEADERS_A', 8192030);
}
if (!defined('PR_DELEGATION')) {
	define('PR_DELEGATION', 8257794);
}
if (!defined('PR_TNEF_CORRELATION_KEY')) {
	define('PR_TNEF_CORRELATION_KEY', 8323330);
}
if (!defined('PR_REPORT_DISPOSITION')) {
	define('PR_REPORT_DISPOSITION', 8388638);
}
if (!defined('PR_REPORT_DISPOSITION_MODE')) {
	define('PR_REPORT_DISPOSITION_MODE', 8454174);
}
if (!defined('PR_EMS_AB_ROOM_CAPACITY')) {
	define('PR_EMS_AB_ROOM_CAPACITY', 134676483);
}
if (!defined('PR_EMS_AB_ROOM_DESCRIPTION')) {
	define('PR_EMS_AB_ROOM_DESCRIPTION', 134807582);
}
if (!defined('PR_CONTENT_INTEGRITY_CHECK')) {
	define('PR_CONTENT_INTEGRITY_CHECK', 201326850);
}
if (!defined('PR_EXPLICIT_CONVERSION')) {
	define('PR_EXPLICIT_CONVERSION', 201392131);
}
if (!defined('PR_IPM_RETURN_REQUESTED')) {
	define('PR_IPM_RETURN_REQUESTED', 201457675);
}
if (!defined('PR_MESSAGE_TOKEN')) {
	define('PR_MESSAGE_TOKEN', 201523458);
}
if (!defined('PR_NDR_REASON_CODE')) {
	define('PR_NDR_REASON_CODE', 201588739);
}
if (!defined('PR_NDR_DIAG_CODE')) {
	define('PR_NDR_DIAG_CODE', 201654275);
}
if (!defined('PR_NON_RECEIPT_NOTIFICATION_REQUESTED')) {
	define('PR_NON_RECEIPT_NOTIFICATION_REQUESTED', 201719819);
}
if (!defined('PR_DELIVERY_POINT')) {
	define('PR_DELIVERY_POINT', 201785347);
}
if (!defined('PR_ORIGINATOR_NON_DELIVERY_REPORT_REQUESTED')) {
	define('PR_ORIGINATOR_NON_DELIVERY_REPORT_REQUESTED', 201850891);
}
if (!defined('PR_ORIGINATOR_REQUESTED_ALTERNATE_RECIPIENT')) {
	define('PR_ORIGINATOR_REQUESTED_ALTERNATE_RECIPIENT', 201916674);
}
if (!defined('PR_PHYSICAL_DELIVERY_BUREAU_FAX_DELIVERY')) {
	define('PR_PHYSICAL_DELIVERY_BUREAU_FAX_DELIVERY', 201981963);
}
if (!defined('PR_PHYSICAL_DELIVERY_MODE')) {
	define('PR_PHYSICAL_DELIVERY_MODE', 202047491);
}
if (!defined('PR_PHYSICAL_DELIVERY_REPORT_REQUEST')) {
	define('PR_PHYSICAL_DELIVERY_REPORT_REQUEST', 202113027);
}
if (!defined('PR_PHYSICAL_FORWARDING_ADDRESS')) {
	define('PR_PHYSICAL_FORWARDING_ADDRESS', 202178818);
}
if (!defined('PR_PHYSICAL_FORWARDING_ADDRESS_REQUESTED')) {
	define('PR_PHYSICAL_FORWARDING_ADDRESS_REQUESTED', 202244107);
}
if (!defined('PR_PHYSICAL_FORWARDING_PROHIBITED')) {
	define('PR_PHYSICAL_FORWARDING_PROHIBITED', 202309643);
}
if (!defined('PR_PHYSICAL_RENDITION_ATTRIBUTES')) {
	define('PR_PHYSICAL_RENDITION_ATTRIBUTES', 202375426);
}
if (!defined('PR_PROOF_OF_DELIVERY')) {
	define('PR_PROOF_OF_DELIVERY', 202440962);
}
if (!defined('PR_PROOF_OF_DELIVERY_REQUESTED')) {
	define('PR_PROOF_OF_DELIVERY_REQUESTED', 202506251);
}
if (!defined('PR_RECIPIENT_CERTIFICATE')) {
	define('PR_RECIPIENT_CERTIFICATE', 202572034);
}
if (!defined('PR_RECIPIENT_NUMBER_FOR_ADVICE')) {
	define('PR_RECIPIENT_NUMBER_FOR_ADVICE', 202637342);
}
if (!defined('PR_RECIPIENT_TYPE')) {
	define('PR_RECIPIENT_TYPE', 202702851);
}
if (!defined('PR_REGISTERED_MAIL_TYPE')) {
	define('PR_REGISTERED_MAIL_TYPE', 202768387);
}
if (!defined('PR_REPLY_REQUESTED')) {
	define('PR_REPLY_REQUESTED', 202833931);
}
if (!defined('PR_REQUESTED_DELIVERY_METHOD')) {
	define('PR_REQUESTED_DELIVERY_METHOD', 202899459);
}
if (!defined('PR_SENDER_ENTRYID')) {
	define('PR_SENDER_ENTRYID', 202965250);
}
if (!defined('PR_SENDER_NAME')) {
	define('PR_SENDER_NAME', 203030558);
}
if (!defined('PR_SENDER_NAME_A')) {
	define('PR_SENDER_NAME_A', 203030558);
}
if (!defined('PR_SUPPLEMENTARY_INFO')) {
	define('PR_SUPPLEMENTARY_INFO', 203096094);
}
if (!defined('PR_TYPE_OF_MTS_USER')) {
	define('PR_TYPE_OF_MTS_USER', 203161603);
}
if (!defined('PR_SENDER_SEARCH_KEY')) {
	define('PR_SENDER_SEARCH_KEY', 203227394);
}
if (!defined('PR_SENDER_ADDRTYPE')) {
	define('PR_SENDER_ADDRTYPE', 203292702);
}
if (!defined('PR_SENDER_ADDRTYPE_A')) {
	define('PR_SENDER_ADDRTYPE_A', 203292702);
}
if (!defined('PR_SENDER_EMAIL_ADDRESS')) {
	define('PR_SENDER_EMAIL_ADDRESS', 203358238);
}
if (!defined('PR_SENDER_EMAIL_ADDRESS_A')) {
	define('PR_SENDER_EMAIL_ADDRESS_A', 203358238);
}
if (!defined('PR_NDR_STATUS_CODE')) {
	define('PR_NDR_STATUS_CODE', 203423747);
}
if (!defined('PR_DSN_REMOTE_MTA')) {
	define('PR_DSN_REMOTE_MTA', 203489310);
}
if (!defined('PR_CURRENT_VERSION')) {
	define('PR_CURRENT_VERSION', 234881044);
}
if (!defined('PR_DELETE_AFTER_SUBMIT')) {
	define('PR_DELETE_AFTER_SUBMIT', 234946571);
}
if (!defined('PR_DISPLAY_BCC')) {
	define('PR_DISPLAY_BCC', 235012126);
}
if (!defined('PR_DISPLAY_BCC_A')) {
	define('PR_DISPLAY_BCC_A', 235012126);
}
if (!defined('PR_DISPLAY_CC')) {
	define('PR_DISPLAY_CC', 235077662);
}
if (!defined('PR_DISPLAY_CC_A')) {
	define('PR_DISPLAY_CC_A', 235077662);
}
if (!defined('PR_DISPLAY_TO')) {
	define('PR_DISPLAY_TO', 235143198);
}
if (!defined('PR_DISPLAY_TO_A')) {
	define('PR_DISPLAY_TO_A', 235143198);
}
if (!defined('PR_PARENT_DISPLAY')) {
	define('PR_PARENT_DISPLAY', 235208734);
}
if (!defined('PR_PARENT_DISPLAY_A')) {
	define('PR_PARENT_DISPLAY_A', 235208734);
}
if (!defined('PR_MESSAGE_DELIVERY_TIME')) {
	define('PR_MESSAGE_DELIVERY_TIME', 235274304);
}
if (!defined('PR_MESSAGE_FLAGS')) {
	define('PR_MESSAGE_FLAGS', 235339779);
}
if (!defined('PR_MESSAGE_SIZE')) {
	define('PR_MESSAGE_SIZE', 235405315);
}
if (!defined('PR_MESSAGE_SIZE_EXTENDED')) {
	define('PR_MESSAGE_SIZE_EXTENDED', 235405332);
}
if (!defined('PR_PARENT_ENTRYID')) {
	define('PR_PARENT_ENTRYID', 235471106);
}
if (!defined('PR_PARENT_SVREID')) {
	define('PR_PARENT_SVREID', 235471099);
}
if (!defined('PR_SENTMAIL_ENTRYID')) {
	define('PR_SENTMAIL_ENTRYID', 235536642);
}
if (!defined('PR_CORRELATE')) {
	define('PR_CORRELATE', 235667467);
}
if (!defined('PR_CORRELATE_MTSID')) {
	define('PR_CORRELATE_MTSID', 235733250);
}
if (!defined('PR_DISCRETE_VALUES')) {
	define('PR_DISCRETE_VALUES', 235798539);
}
if (!defined('PR_RESPONSIBILITY')) {
	define('PR_RESPONSIBILITY', 235864075);
}
if (!defined('PR_SPOOLER_STATUS')) {
	define('PR_SPOOLER_STATUS', 235929603);
}
if (!defined('PR_TRANSPORT_STATUS')) {
	define('PR_TRANSPORT_STATUS', 235995139);
}
if (!defined('PR_MESSAGE_RECIPIENTS')) {
	define('PR_MESSAGE_RECIPIENTS', 236060685);
}
if (!defined('PR_MESSAGE_ATTACHMENTS')) {
	define('PR_MESSAGE_ATTACHMENTS', 236126221);
}
if (!defined('PR_SUBMIT_FLAGS')) {
	define('PR_SUBMIT_FLAGS', 236191747);
}
if (!defined('PR_RECIPIENT_STATUS')) {
	define('PR_RECIPIENT_STATUS', 236257283);
}
if (!defined('PR_TRANSPORT_KEY')) {
	define('PR_TRANSPORT_KEY', 236322819);
}
if (!defined('PR_MSG_STATUS')) {
	define('PR_MSG_STATUS', 236388355);
}
if (!defined('PR_MESSAGE_DOWNLOAD_TIME')) {
	define('PR_MESSAGE_DOWNLOAD_TIME', 236453891);
}
if (!defined('PR_CREATION_VERSION')) {
	define('PR_CREATION_VERSION', 236519444);
}
if (!defined('PR_MODIFY_VERSION')) {
	define('PR_MODIFY_VERSION', 236584980);
}
if (!defined('PR_HASATTACH')) {
	define('PR_HASATTACH', 236650507);
}
if (!defined('PR_BODY_CRC')) {
	define('PR_BODY_CRC', 236716035);
}
if (!defined('PR_NORMALIZED_SUBJECT')) {
	define('PR_NORMALIZED_SUBJECT', 236781598);
}
if (!defined('PR_NORMALIZED_SUBJECT_A')) {
	define('PR_NORMALIZED_SUBJECT_A', 236781598);
}
if (!defined('PR_RTF_IN_SYNC')) {
	define('PR_RTF_IN_SYNC', 236912651);
}
if (!defined('PR_ATTACH_SIZE')) {
	define('PR_ATTACH_SIZE', 236978179);
}
if (!defined('PR_ATTACH_NUM')) {
	define('PR_ATTACH_NUM', 237043715);
}
if (!defined('PR_PREPROCESS')) {
	define('PR_PREPROCESS', 237109259);
}
if (!defined('PR_INTERNET_ARTICLE_NUMBER')) {
	define('PR_INTERNET_ARTICLE_NUMBER', 237174787);
}
if (!defined('PR_NEWSGROUP_NAME')) {
	define('PR_NEWSGROUP_NAME', 237240350);
}
if (!defined('PR_ORIGINATING_MTA_CERTIFICATE')) {
	define('PR_ORIGINATING_MTA_CERTIFICATE', 237306114);
}
if (!defined('PR_PROOF_OF_SUBMISSION')) {
	define('PR_PROOF_OF_SUBMISSION', 237371650);
}
if (!defined('PR_NT_SECURITY_DESCRIPTOR')) {
	define('PR_NT_SECURITY_DESCRIPTOR', 237437186);
}
if (!defined('PR_PRIMARY_SEND_ACCOUNT')) {
	define('PR_PRIMARY_SEND_ACCOUNT', 237502494);
}
if (!defined('PR_NEXT_SEND_ACCT')) {
	define('PR_NEXT_SEND_ACCT', 237568030);
}
if (!defined('PR_TODO_ITEM_FLAGS')) {
	define('PR_TODO_ITEM_FLAGS', 237699075);
}
if (!defined('PR_SWAPPED_TODO_STORE')) {
	define('PR_SWAPPED_TODO_STORE', 237764866);
}
if (!defined('PR_SWAPPED_TODO_DATA')) {
	define('PR_SWAPPED_TODO_DATA', 237830402);
}
if (!defined('PR_REPL_ITEMID')) {
	define('PR_REPL_ITEMID', 238026755);
}
if (!defined('PR_REPL_CHANGENUM')) {
	define('PR_REPL_CHANGENUM', 238223380);
}
if (!defined('PR_REPL_VERSIONHISTORY')) {
	define('PR_REPL_VERSIONHISTORY', 238289154);
}
if (!defined('PR_REPL_FLAGS')) {
	define('PR_REPL_FLAGS', 238551043);
}
if (!defined('PR_REPL_COPIEDFROM_VERSIONHISTORY')) {
	define('PR_REPL_COPIEDFROM_VERSIONHISTORY', 238813442);
}
if (!defined('PR_REPL_COPIEDFROM_ITEMID')) {
	define('PR_REPL_COPIEDFROM_ITEMID', 238878978);
}
if (!defined('PR_CREATOR_SID')) {
	define('PR_CREATOR_SID', 240648450);
}
if (!defined('PR_LAST_MODIFIER_SID')) {
	define('PR_LAST_MODIFIER_SID', 240713986);
}
if (!defined('PR_READ')) {
	define('PR_READ', 241762315);
}
if (!defined('PR_NT_SECURITY_DESCRIPTOR_AS_XML')) {
	define('PR_NT_SECURITY_DESCRIPTOR_AS_XML', 241827870);
}
if (!defined('PR_TRUST_SENDER')) {
	define('PR_TRUST_SENDER', 242810883);
}
if (!defined('PR_EXTENDED_RULE_MSG_ACTIONS')) {
	define('PR_EXTENDED_RULE_MSG_ACTIONS', 244908290);
}
if (!defined('PR_EXTENDED_RULE_MSG_CONDITION')) {
	define('PR_EXTENDED_RULE_MSG_CONDITION', 244973826);
}
if (!defined('PR_EXTENDED_RULE_SIZE_LIMIT')) {
	define('PR_EXTENDED_RULE_SIZE_LIMIT', 245039107);
}
if (!defined('PR_ASSOCIATED_SHARING_PROVIDER')) {
	define('PR_ASSOCIATED_SHARING_PROVIDER', 245366856);
}
if (!defined('PR_SEARCH_ATTACHMENTS')) {
	define('PR_SEARCH_ATTACHMENTS', 245694494);
}
if (!defined('PR_SEARCH_RECIP_EMAIL_TO')) {
	define('PR_SEARCH_RECIP_EMAIL_TO', 245760030);
}
if (!defined('PR_SEARCH_RECIP_EMAIL_CC')) {
	define('PR_SEARCH_RECIP_EMAIL_CC', 245825566);
}
if (!defined('PR_SEARCH_RECIP_EMAIL_BCC')) {
	define('PR_SEARCH_RECIP_EMAIL_BCC', 245891102);
}
if (!defined('PR_ACCESS')) {
	define('PR_ACCESS', 267649027);
}
if (!defined('PR_ROW_TYPE')) {
	define('PR_ROW_TYPE', 267714563);
}
if (!defined('PR_INSTANCE_KEY')) {
	define('PR_INSTANCE_KEY', 267780354);
}
if (!defined('PR_INSTANCE_SVREID')) {
	define('PR_INSTANCE_SVREID', 267780347);
}
if (!defined('PR_ACCESS_LEVEL')) {
	define('PR_ACCESS_LEVEL', 267845635);
}
if (!defined('PR_MAPPING_SIGNATURE')) {
	define('PR_MAPPING_SIGNATURE', 267911426);
}
if (!defined('PR_RECORD_KEY')) {
	define('PR_RECORD_KEY', 267976962);
}
if (!defined('PR_STORE_RECORD_KEY')) {
	define('PR_STORE_RECORD_KEY', 268042498);
}
if (!defined('PR_STORE_ENTRYID')) {
	define('PR_STORE_ENTRYID', 268108034);
}
if (!defined('PR_MINI_ICON')) {
	define('PR_MINI_ICON', 268173570);
}
if (!defined('PR_ICON')) {
	define('PR_ICON', 268239106);
}
if (!defined('PR_OBJECT_TYPE')) {
	define('PR_OBJECT_TYPE', 268304387);
}
if (!defined('PR_ENTRYID')) {
	define('PR_ENTRYID', 268370178);
}
if (!defined('PR_BODY')) {
	define('PR_BODY', 268435486);
}
if (!defined('PR_BODY_A')) {
	define('PR_BODY_A', 268435486);
}
if (!defined('PR_BODY_W')) {
	define('PR_BODY_W', 268435486);
}
if (!defined('PR_REPORT_TEXT')) {
	define('PR_REPORT_TEXT', 268501022);
}
if (!defined('PR_ORIGINATOR_AND_DL_EXPANSION_HISTORY')) {
	define('PR_ORIGINATOR_AND_DL_EXPANSION_HISTORY', 268566786);
}
if (!defined('PR_REPORTING_DL_NAME')) {
	define('PR_REPORTING_DL_NAME', 268632322);
}
if (!defined('PR_REPORTING_MTA_CERTIFICATE')) {
	define('PR_REPORTING_MTA_CERTIFICATE', 268697858);
}
if (!defined('PR_RTF_SYNC_BODY_CRC')) {
	define('PR_RTF_SYNC_BODY_CRC', 268828675);
}
if (!defined('PR_RTF_SYNC_BODY_COUNT')) {
	define('PR_RTF_SYNC_BODY_COUNT', 268894211);
}
if (!defined('PR_RTF_SYNC_BODY_TAG')) {
	define('PR_RTF_SYNC_BODY_TAG', 268959774);
}
if (!defined('PR_RTF_COMPRESSED')) {
	define('PR_RTF_COMPRESSED', 269025538);
}
if (!defined('PR_RTF_SYNC_PREFIX_COUNT')) {
	define('PR_RTF_SYNC_PREFIX_COUNT', 269484035);
}
if (!defined('PR_RTF_SYNC_TRAILING_COUNT')) {
	define('PR_RTF_SYNC_TRAILING_COUNT', 269549571);
}
if (!defined('PR_ORIGINALLY_INTENDED_RECIP_ENTRYID')) {
	define('PR_ORIGINALLY_INTENDED_RECIP_ENTRYID', 269615362);
}
if (!defined('PR_BODY_HTML')) {
	define('PR_BODY_HTML', 269680670);
}
if (!defined('PR_BODY_HTML_A')) {
	define('PR_BODY_HTML_A', 269680670);
}
if (!defined('PR_HTML')) {
	define('PR_HTML', 269680898);
}
if (!defined('PR_BODY_CONTENT_LOCATION')) {
	define('PR_BODY_CONTENT_LOCATION', 269746206);
}
if (!defined('PR_BODY_CONTENT_LOCATION_A')) {
	define('PR_BODY_CONTENT_LOCATION_A', 269746206);
}
if (!defined('PR_BODY_CONTENT_ID')) {
	define('PR_BODY_CONTENT_ID', 269811742);
}
if (!defined('PR_BODY_CONTENT_ID_A')) {
	define('PR_BODY_CONTENT_ID_A', 269811742);
}
if (!defined('PR_NATIVE_BODY_INFO')) {
	define('PR_NATIVE_BODY_INFO', 269877251);
}
if (!defined('PR_INTERNET_APPROVED')) {
	define('PR_INTERNET_APPROVED', 271581214);
}
if (!defined('PR_INTERNET_CONTROL')) {
	define('PR_INTERNET_CONTROL', 271646750);
}
if (!defined('PR_INTERNET_DISTRIBUTION')) {
	define('PR_INTERNET_DISTRIBUTION', 271712286);
}
if (!defined('PR_INTERNET_FOLLOWUP_TO')) {
	define('PR_INTERNET_FOLLOWUP_TO', 271777822);
}
if (!defined('PR_INTERNET_LINES')) {
	define('PR_INTERNET_LINES', 271843331);
}
if (!defined('PR_INTERNET_MESSAGE_ID')) {
	define('PR_INTERNET_MESSAGE_ID', 271908894);
}
if (!defined('PR_INTERNET_MESSAGE_ID_A')) {
	define('PR_INTERNET_MESSAGE_ID_A', 271908894);
}
if (!defined('PR_INTERNET_NEWSGROUPS')) {
	define('PR_INTERNET_NEWSGROUPS', 271974430);
}
if (!defined('PR_INTERNET_ORGANIZATION')) {
	define('PR_INTERNET_ORGANIZATION', 272039966);
}
if (!defined('PR_INTERNET_NNTP_PATH')) {
	define('PR_INTERNET_NNTP_PATH', 272105502);
}
if (!defined('PR_INTERNET_REFERENCES')) {
	define('PR_INTERNET_REFERENCES', 272171038);
}
if (!defined('PR_INTERNET_REFERENCES_A')) {
	define('PR_INTERNET_REFERENCES_A', 272171038);
}
if (!defined('PR_SUPERSEDES')) {
	define('PR_SUPERSEDES', 272236574);
}
if (!defined('PR_POST_FOLDER_ENTRIES')) {
	define('PR_POST_FOLDER_ENTRIES', 272302338);
}
if (!defined('PR_POST_FOLDER_NAMES')) {
	define('PR_POST_FOLDER_NAMES', 272367646);
}
if (!defined('PR_POST_REPLY_FOLDER_ENTRIES')) {
	define('PR_POST_REPLY_FOLDER_ENTRIES', 272433410);
}
if (!defined('PR_POST_REPLY_FOLDER_NAMES')) {
	define('PR_POST_REPLY_FOLDER_NAMES', 272498718);
}
if (!defined('PR_POST_REPLY_DENIED')) {
	define('PR_POST_REPLY_DENIED', 272564482);
}
if (!defined('PR_NNTP_XREF')) {
	define('PR_NNTP_XREF', 272629790);
}
if (!defined('PR_INTERNET_PRECEDENCE')) {
	define('PR_INTERNET_PRECEDENCE', 272695326);
}
if (!defined('PR_IN_REPLY_TO_ID')) {
	define('PR_IN_REPLY_TO_ID', 272760862);
}
if (!defined('PR_IN_REPLY_TO_ID_A')) {
	define('PR_IN_REPLY_TO_ID_A', 272760862);
}
if (!defined('PR_LIST_HELP')) {
	define('PR_LIST_HELP', 272826398);
}
if (!defined('PR_LIST_HELP_A')) {
	define('PR_LIST_HELP_A', 272826398);
}
if (!defined('PR_LIST_SUBSCRIBE')) {
	define('PR_LIST_SUBSCRIBE', 272891934);
}
if (!defined('PR_LIST_SUBSCRIBE_A')) {
	define('PR_LIST_SUBSCRIBE_A', 272891934);
}
if (!defined('PR_LIST_UNSUBSCRIBE')) {
	define('PR_LIST_UNSUBSCRIBE', 272957470);
}
if (!defined('PR_LIST_UNSUBSCRIBE_A')) {
	define('PR_LIST_UNSUBSCRIBE_A', 272957470);
}
if (!defined('PR_INTERNET_RETURN_PATH')) {
	define('PR_INTERNET_RETURN_PATH', 273023006);
}
if (!defined('PR_ICON_INDEX')) {
	define('PR_ICON_INDEX', 276824067);
}
if (!defined('PR_LAST_VERB_EXECUTED')) {
	define('PR_LAST_VERB_EXECUTED', 276889603);
}
if (!defined('PR_LAST_VERB_EXECUTION_TIME')) {
	define('PR_LAST_VERB_EXECUTION_TIME', 276955200);
}
if (!defined('PR_FLAG_STATUS')) {
	define('PR_FLAG_STATUS', 277872643);
}
if (!defined('PR_FLAG_COMPLETE_TIME')) {
	define('PR_FLAG_COMPLETE_TIME', 277938240);
}
if (!defined('PR_FOLLOWUP_ICON')) {
	define('PR_FOLLOWUP_ICON', 278200323);
}
if (!defined('PR_BLOCK_STATUS')) {
	define('PR_BLOCK_STATUS', 278265859);
}
if (!defined('PR_ITEM_TMPFLAGS')) {
	define('PR_ITEM_TMPFLAGS', 278331395);
}
if (!defined('PR_CONFLICT_ITEMS')) {
	define('PR_CONFLICT_ITEMS', 278401282);
}
if (!defined('PR_CDO_RECURRENCEID')) {
	define('PR_CDO_RECURRENCEID', 281346112);
}
if (!defined('PR_ATTR_HIDDEN')) {
	define('PR_ATTR_HIDDEN', 284426251);
}
if (!defined('PR_ATTR_HIDDEN_GROMOX')) {
	define('PR_ATTR_HIDDEN_GROMOX', 284426243);
}
if (!defined('PR_ATTR_SYSTEM')) {
	define('PR_ATTR_SYSTEM', 284491787);
}
if (!defined('PR_ATTR_READONLY')) {
	define('PR_ATTR_READONLY', 284557323);
}
if (!defined('PR_ROWID')) {
	define('PR_ROWID', 805306371);
}
if (!defined('PR_DISPLAY_NAME')) {
	define('PR_DISPLAY_NAME', 805371934);
}
if (!defined('PR_DISPLAY_NAME_A')) {
	define('PR_DISPLAY_NAME_A', 805371934);
}
if (!defined('PR_ADDRTYPE')) {
	define('PR_ADDRTYPE', 805437470);
}
if (!defined('PR_ADDRTYPE_A')) {
	define('PR_ADDRTYPE_A', 805437470);
}
if (!defined('PR_EMAIL_ADDRESS')) {
	define('PR_EMAIL_ADDRESS', 805503006);
}
if (!defined('PR_EMAIL_ADDRESS_A')) {
	define('PR_EMAIL_ADDRESS_A', 805503006);
}
if (!defined('PR_COMMENT')) {
	define('PR_COMMENT', 805568542);
}
if (!defined('PR_DEPTH')) {
	define('PR_DEPTH', 805634051);
}
if (!defined('PR_PROVIDER_DISPLAY')) {
	define('PR_PROVIDER_DISPLAY', 805699614);
}
if (!defined('PR_CREATION_TIME')) {
	define('PR_CREATION_TIME', 805765184);
}
if (!defined('PR_LAST_MODIFICATION_TIME')) {
	define('PR_LAST_MODIFICATION_TIME', 805830720);
}
if (!defined('PR_RESOURCE_FLAGS')) {
	define('PR_RESOURCE_FLAGS', 805896195);
}
if (!defined('PR_PROVIDER_DLL_NAME')) {
	define('PR_PROVIDER_DLL_NAME', 805961758);
}
if (!defined('PR_SEARCH_KEY')) {
	define('PR_SEARCH_KEY', 806027522);
}
if (!defined('PR_PROVIDER_UID')) {
	define('PR_PROVIDER_UID', 806093058);
}
if (!defined('PR_PROVIDER_ORDINAL')) {
	define('PR_PROVIDER_ORDINAL', 806158339);
}
if (!defined('PR_TARGET_ENTRYID')) {
	define('PR_TARGET_ENTRYID', 806355202);
}
if (!defined('PR_CONVERSATION_ID')) {
	define('PR_CONVERSATION_ID', 806551810);
}
if (!defined('PR_CONVERSATION_INDEX_TRACKING')) {
	define('PR_CONVERSATION_INDEX_TRACKING', 806748171);
}
if (!defined('PR_ARCHIVE_TAG')) {
	define('PR_ARCHIVE_TAG', 806879490);
}
if (!defined('PR_POLICY_TAG')) {
	define('PR_POLICY_TAG', 806945026);
}
if (!defined('PR_RETENTION_PERIOD')) {
	define('PR_RETENTION_PERIOD', 807010307);
}
if (!defined('PR_START_DATE_ETC')) {
	define('PR_START_DATE_ETC', 807076098);
}
if (!defined('PR_RETENTION_DATE')) {
	define('PR_RETENTION_DATE', 807141440);
}
if (!defined('PR_RETENTION_FLAGS')) {
	define('PR_RETENTION_FLAGS', 807206915);
}
if (!defined('PR_ARCHIVE_PERIOD')) {
	define('PR_ARCHIVE_PERIOD', 807272451);
}
if (!defined('PR_ARCHIVE_DATE')) {
	define('PR_ARCHIVE_DATE', 807338048);
}
if (!defined('PR_SORT_POSITION')) {
	define('PR_SORT_POSITION', 807403778);
}
if (!defined('PR_SORT_PARENTID')) {
	define('PR_SORT_PARENTID', 807469314);
}
if (!defined('PR_FORM_VERSION')) {
	define('PR_FORM_VERSION', 855703582);
}
if (!defined('PR_FORM_CLSID')) {
	define('PR_FORM_CLSID', 855769160);
}
if (!defined('PR_FORM_CONTACT_NAME')) {
	define('PR_FORM_CONTACT_NAME', 855834654);
}
if (!defined('PR_FORM_CATEGORY')) {
	define('PR_FORM_CATEGORY', 855900190);
}
if (!defined('PR_FORM_CATEGORY_SUB')) {
	define('PR_FORM_CATEGORY_SUB', 855965726);
}
if (!defined('PR_FORM_HOST_MAP')) {
	define('PR_FORM_HOST_MAP', 856035331);
}
if (!defined('PR_FORM_HIDDEN')) {
	define('PR_FORM_HIDDEN', 856096779);
}
if (!defined('PR_FORM_DESIGNER_NAME')) {
	define('PR_FORM_DESIGNER_NAME', 856162334);
}
if (!defined('PR_FORM_DESIGNER_GUID')) {
	define('PR_FORM_DESIGNER_GUID', 856227912);
}
if (!defined('PR_FORM_MESSAGE_BEHAVIOR')) {
	define('PR_FORM_MESSAGE_BEHAVIOR', 856293379);
}
if (!defined('PR_DEFAULT_STORE')) {
	define('PR_DEFAULT_STORE', 872415243);
}
if (!defined('PR_STORE_SUPPORT_MASK')) {
	define('PR_STORE_SUPPORT_MASK', 873267203);
}
if (!defined('PR_STORE_STATE')) {
	define('PR_STORE_STATE', 873332739);
}
if (!defined('PR_STORE_UNICODE_MASK')) {
	define('PR_STORE_UNICODE_MASK', 873398275);
}
if (!defined('PR_IPM_SUBTREE_SEARCH_KEY')) {
	define('PR_IPM_SUBTREE_SEARCH_KEY', 873464066);
}
if (!defined('PR_IPM_OUTBOX_SEARCH_KEY')) {
	define('PR_IPM_OUTBOX_SEARCH_KEY', 873529602);
}
if (!defined('PR_IPM_WASTEBASKET_SEARCH_KEY')) {
	define('PR_IPM_WASTEBASKET_SEARCH_KEY', 873595138);
}
if (!defined('PR_IPM_SENTMAIL_SEARCH_KEY')) {
	define('PR_IPM_SENTMAIL_SEARCH_KEY', 873660674);
}
if (!defined('PR_MDB_PROVIDER')) {
	define('PR_MDB_PROVIDER', 873726210);
}
if (!defined('PR_RECEIVE_FOLDER_SETTINGS')) {
	define('PR_RECEIVE_FOLDER_SETTINGS', 873791501);
}
if (!defined('PR_QUOTA_WARNING')) {
	define('PR_QUOTA_WARNING', 873988099);
}
if (!defined('PR_QUOTA_SEND')) {
	define('PR_QUOTA_SEND', 874053635);
}
if (!defined('PR_QUOTA_RECEIVE')) {
	define('PR_QUOTA_RECEIVE', 874119171);
}
if (!defined('PR_ROOT_ENTRYID')) {
	define('PR_ROOT_ENTRYID', 903348482);
}
if (!defined('PR_VALID_FOLDER_MASK')) {
	define('PR_VALID_FOLDER_MASK', 903806979);
}
if (!defined('PR_IPM_SUBTREE_ENTRYID')) {
	define('PR_IPM_SUBTREE_ENTRYID', 903872770);
}
if (!defined('PR_IPM_INBOX_ENTRYID')) {
	define('PR_IPM_INBOX_ENTRYID', 903938306);
}
if (!defined('PR_IPM_OUTBOX_ENTRYID')) {
	define('PR_IPM_OUTBOX_ENTRYID', 904003842);
}
if (!defined('PR_IPM_WASTEBASKET_ENTRYID')) {
	define('PR_IPM_WASTEBASKET_ENTRYID', 904069378);
}
if (!defined('PR_IPM_SENTMAIL_ENTRYID')) {
	define('PR_IPM_SENTMAIL_ENTRYID', 904134914);
}
if (!defined('PR_VIEWS_ENTRYID')) {
	define('PR_VIEWS_ENTRYID', 904200450);
}
if (!defined('PR_COMMON_VIEWS_ENTRYID')) {
	define('PR_COMMON_VIEWS_ENTRYID', 904265986);
}
if (!defined('PR_FINDER_ENTRYID')) {
	define('PR_FINDER_ENTRYID', 904331522);
}
if (!defined('PR_SYNC_ROOT_ENTRYID')) {
	define('PR_SYNC_ROOT_ENTRYID', 904528130);
}
if (!defined('PR_VOICEMAIL_FOLDER_ENTRYID')) {
	define('PR_VOICEMAIL_FOLDER_ENTRYID', 904593666);
}
if (!defined('PR_CONTAINER_FLAGS')) {
	define('PR_CONTAINER_FLAGS', 905969667);
}
if (!defined('PR_FOLDER_TYPE')) {
	define('PR_FOLDER_TYPE', 906035203);
}
if (!defined('PR_CONTENT_COUNT')) {
	define('PR_CONTENT_COUNT', 906100739);
}
if (!defined('PR_CONTENT_UNREAD')) {
	define('PR_CONTENT_UNREAD', 906166275);
}
if (!defined('PR_CREATE_TEMPLATES')) {
	define('PR_CREATE_TEMPLATES', 906231821);
}
if (!defined('PR_SEARCH')) {
	define('PR_SEARCH', 906428429);
}
if (!defined('PR_SELECTABLE')) {
	define('PR_SELECTABLE', 906559499);
}
if (!defined('PR_SUBFOLDERS')) {
	define('PR_SUBFOLDERS', 906625035);
}
if (!defined('PR_STATUS')) {
	define('PR_STATUS', 906690563);
}
if (!defined('PR_ANR')) {
	define('PR_ANR', 906756126);
}
if (!defined('PR_ANR_A')) {
	define('PR_ANR_A', 906756126);
}
if (!defined('PR_CONTENTS_SORT_ORDER')) {
	define('PR_CONTENTS_SORT_ORDER', 906825731);
}
if (!defined('PR_CONTAINER_HIERARCHY')) {
	define('PR_CONTAINER_HIERARCHY', 906887181);
}
if (!defined('PR_CONTAINER_CONTENTS')) {
	define('PR_CONTAINER_CONTENTS', 906952717);
}
if (!defined('PR_FOLDER_ASSOCIATED_CONTENTS')) {
	define('PR_FOLDER_ASSOCIATED_CONTENTS', 907018253);
}
if (!defined('PR_DEF_CREATE_DL')) {
	define('PR_DEF_CREATE_DL', 907084034);
}
if (!defined('PR_DEF_CREATE_MAILUSER')) {
	define('PR_DEF_CREATE_MAILUSER', 907149570);
}
if (!defined('PR_CONTAINER_CLASS')) {
	define('PR_CONTAINER_CLASS', 907214878);
}
if (!defined('PR_CONTAINER_MODIFY_VERSION')) {
	define('PR_CONTAINER_MODIFY_VERSION', 907280404);
}
if (!defined('PR_AB_PROVIDER_ID')) {
	define('PR_AB_PROVIDER_ID', 907346178);
}
if (!defined('PR_DEFAULT_VIEW_ENTRYID')) {
	define('PR_DEFAULT_VIEW_ENTRYID', 907411714);
}
if (!defined('PR_ASSOC_CONTENT_COUNT')) {
	define('PR_ASSOC_CONTENT_COUNT', 907476995);
}
if (!defined('PR_IPM_APPOINTMENT_ENTRYID')) {
	define('PR_IPM_APPOINTMENT_ENTRYID', 919601410);
}
if (!defined('PR_IPM_CONTACT_ENTRYID')) {
	define('PR_IPM_CONTACT_ENTRYID', 919666946);
}
if (!defined('PR_IPM_JOURNAL_ENTRYID')) {
	define('PR_IPM_JOURNAL_ENTRYID', 919732482);
}
if (!defined('PR_IPM_NOTE_ENTRYID')) {
	define('PR_IPM_NOTE_ENTRYID', 919798018);
}
if (!defined('PR_IPM_TASK_ENTRYID')) {
	define('PR_IPM_TASK_ENTRYID', 919863554);
}
if (!defined('PR_REM_ONLINE_ENTRYID')) {
	define('PR_REM_ONLINE_ENTRYID', 919929090);
}
if (!defined('PR_REM_OFFLINE_ENTRYID')) {
	define('PR_REM_OFFLINE_ENTRYID', 919994626);
}
if (!defined('PR_IPM_DRAFTS_ENTRYID')) {
	define('PR_IPM_DRAFTS_ENTRYID', 920060162);
}
if (!defined('PR_ADDITIONAL_REN_ENTRYIDS')) {
	define('PR_ADDITIONAL_REN_ENTRYIDS', 920129794);
}
if (!defined('PR_ADDITIONAL_REN_ENTRYIDS_EX')) {
	define('PR_ADDITIONAL_REN_ENTRYIDS_EX', 920191234);
}
if (!defined('PR_EXTENDED_FOLDER_FLAGS')) {
	define('PR_EXTENDED_FOLDER_FLAGS', 920256770);
}
if (!defined('PR_NET_FOLDER_FLAGS')) {
	define('PR_NET_FOLDER_FLAGS', 920518659);
}
if (!defined('PR_FOLDER_WEBVIEWINFO')) {
	define('PR_FOLDER_WEBVIEWINFO', 920584450);
}
if (!defined('PR_FOLDER_XVIEWINFO_E')) {
	define('PR_FOLDER_XVIEWINFO_E', 920649986);
}
if (!defined('PR_FOLDER_VIEWS_ONLY')) {
	define('PR_FOLDER_VIEWS_ONLY', 920715267);
}
if (!defined('PR_ORDINAL_MOST')) {
	define('PR_ORDINAL_MOST', 920780803);
}
if (!defined('PR_FREEBUSY_ENTRYIDS')) {
	define('PR_FREEBUSY_ENTRYIDS', 920916226);
}
if (!defined('PR_DEF_POST_MSGCLASS')) {
	define('PR_DEF_POST_MSGCLASS', 920977438);
}
if (!defined('PR_DEF_POST_DISPLAYNAME')) {
	define('PR_DEF_POST_DISPLAYNAME', 921042974);
}
if (!defined('PR_GENERATE_EXCHANGE_VIEWS')) {
	define('PR_GENERATE_EXCHANGE_VIEWS', 921239563);
}
if (!defined('PR_ATTACHMENT_X400_PARAMETERS')) {
	define('PR_ATTACHMENT_X400_PARAMETERS', 922747138);
}
if (!defined('PR_ATTACH_DATA_BIN')) {
	define('PR_ATTACH_DATA_BIN', 922812674);
}
if (!defined('PR_ATTACH_DATA_OBJ')) {
	define('PR_ATTACH_DATA_OBJ', 922812429);
}
if (!defined('PR_ATTACH_ENCODING')) {
	define('PR_ATTACH_ENCODING', 922878210);
}
if (!defined('PR_ATTACH_EXTENSION')) {
	define('PR_ATTACH_EXTENSION', 922943518);
}
if (!defined('PR_ATTACH_EXTENSION_A')) {
	define('PR_ATTACH_EXTENSION_A', 922943518);
}
if (!defined('PR_ATTACH_FILENAME')) {
	define('PR_ATTACH_FILENAME', 923009054);
}
if (!defined('PR_ATTACH_FILENAME_A')) {
	define('PR_ATTACH_FILENAME_A', 923009054);
}
if (!defined('PR_ATTACH_METHOD')) {
	define('PR_ATTACH_METHOD', 923074563);
}
if (!defined('PR_ATTACH_LONG_FILENAME')) {
	define('PR_ATTACH_LONG_FILENAME', 923205662);
}
if (!defined('PR_ATTACH_LONG_FILENAME_A')) {
	define('PR_ATTACH_LONG_FILENAME_A', 923205662);
}
if (!defined('PR_ATTACH_PATHNAME')) {
	define('PR_ATTACH_PATHNAME', 923271198);
}
if (!defined('PR_ATTACH_RENDERING')) {
	define('PR_ATTACH_RENDERING', 923336962);
}
if (!defined('PR_ATTACH_TAG')) {
	define('PR_ATTACH_TAG', 923402498);
}
if (!defined('PR_RENDERING_POSITION')) {
	define('PR_RENDERING_POSITION', 923467779);
}
if (!defined('PR_ATTACH_TRANSPORT_NAME_A')) {
	define('PR_ATTACH_TRANSPORT_NAME_A', 923533342);
}
if (!defined('PR_ATTACH_TRANSPORT_NAME')) {
	define('PR_ATTACH_TRANSPORT_NAME', 923533342);
}
if (!defined('PR_ATTACH_LONG_PATHNAME')) {
	define('PR_ATTACH_LONG_PATHNAME', 923598878);
}
if (!defined('PR_ATTACH_MIME_TAG')) {
	define('PR_ATTACH_MIME_TAG', 923664414);
}
if (!defined('PR_ATTACH_ADDITIONAL_INFO')) {
	define('PR_ATTACH_ADDITIONAL_INFO', 923730178);
}
if (!defined('PR_ATTACH_CONTENT_BASE')) {
	define('PR_ATTACH_CONTENT_BASE', 923861022);
}
if (!defined('PR_ATTACH_CONTENT_BASE_A')) {
	define('PR_ATTACH_CONTENT_BASE_A', 923861022);
}
if (!defined('PR_ATTACH_CONTENT_ID')) {
	define('PR_ATTACH_CONTENT_ID', 923926558);
}
if (!defined('PR_ATTACH_CONTENT_ID_A')) {
	define('PR_ATTACH_CONTENT_ID_A', 923926558);
}
if (!defined('PR_ATTACH_CONTENT_LOCATION')) {
	define('PR_ATTACH_CONTENT_LOCATION', 923992094);
}
if (!defined('PR_ATTACH_CONTENT_LOCATION_A')) {
	define('PR_ATTACH_CONTENT_LOCATION_A', 923992094);
}
if (!defined('PR_ATTACH_FLAGS')) {
	define('PR_ATTACH_FLAGS', 924057603);
}
if (!defined('PR_ATTACH_PAYLOAD_PROV_GUID_STR')) {
	define('PR_ATTACH_PAYLOAD_PROV_GUID_STR', 924385310);
}
if (!defined('PR_ATTACH_PAYLOAD_CLASS')) {
	define('PR_ATTACH_PAYLOAD_CLASS', 924450846);
}
if (!defined('PR_ATTACH_PAYLOAD_CLASS_A')) {
	define('PR_ATTACH_PAYLOAD_CLASS_A', 924450846);
}
if (!defined('PR_DISPLAY_TYPE')) {
	define('PR_DISPLAY_TYPE', 956301315);
}
if (!defined('PR_TEMPLATEID')) {
	define('PR_TEMPLATEID', 956432642);
}
if (!defined('PR_PRIMARY_CAPABILITY')) {
	define('PR_PRIMARY_CAPABILITY', 956563714);
}
if (!defined('PR_DISPLAY_TYPE_EX')) {
	define('PR_DISPLAY_TYPE_EX', 956628995);
}
if (!defined('PR_SMTP_ADDRESS')) {
	define('PR_SMTP_ADDRESS', 972947486);
}
if (!defined('PR_SMTP_ADDRESS_A')) {
	define('PR_SMTP_ADDRESS_A', 972947486);
}
if (!defined('PR_EMS_AB_DISPLAY_NAME_PRINTABLE')) {
	define('PR_EMS_AB_DISPLAY_NAME_PRINTABLE', 973013022);
}
if (!defined('PR_EMS_AB_DISPLAY_NAME_PRINTABLE_A')) {
	define('PR_EMS_AB_DISPLAY_NAME_PRINTABLE_A', 973013022);
}
if (!defined('PR_7BIT_DISPLAY_NAME')) {
	define('PR_7BIT_DISPLAY_NAME', 973013022);
}
if (!defined('PR_ACCOUNT')) {
	define('PR_ACCOUNT', 973078558);
}
if (!defined('PR_ACCOUNT_A')) {
	define('PR_ACCOUNT_A', 973078558);
}
if (!defined('PR_ALTERNATE_RECIPIENT')) {
	define('PR_ALTERNATE_RECIPIENT', 973144322);
}
if (!defined('PR_CALLBACK_TELEPHONE_NUMBER')) {
	define('PR_CALLBACK_TELEPHONE_NUMBER', 973209630);
}
if (!defined('PR_CONVERSION_PROHIBITED')) {
	define('PR_CONVERSION_PROHIBITED', 973275147);
}
if (!defined('PR_DISCLOSE_RECIPIENTS')) {
	define('PR_DISCLOSE_RECIPIENTS', 973340683);
}
if (!defined('PR_GENERATION')) {
	define('PR_GENERATION', 973406238);
}
if (!defined('PR_GIVEN_NAME')) {
	define('PR_GIVEN_NAME', 973471774);
}
if (!defined('PR_GOVERNMENT_ID_NUMBER')) {
	define('PR_GOVERNMENT_ID_NUMBER', 973537310);
}
if (!defined('PR_BUSINESS_TELEPHONE_NUMBER')) {
	define('PR_BUSINESS_TELEPHONE_NUMBER', 973602846);
}
if (!defined('PR_OFFICE_TELEPHONE_NUMBER')) {
	define('PR_OFFICE_TELEPHONE_NUMBER', 973602846);
}
if (!defined('PR_HOME_TELEPHONE_NUMBER')) {
	define('PR_HOME_TELEPHONE_NUMBER', 973668382);
}
if (!defined('PR_INITIALS')) {
	define('PR_INITIALS', 973733918);
}
if (!defined('PR_KEYWORD')) {
	define('PR_KEYWORD', 973799454);
}
if (!defined('PR_LANGUAGE')) {
	define('PR_LANGUAGE', 973864990);
}
if (!defined('PR_LOCATION')) {
	define('PR_LOCATION', 973930526);
}
if (!defined('PR_MAIL_PERMISSION')) {
	define('PR_MAIL_PERMISSION', 973996043);
}
if (!defined('PR_MHS_COMMON_NAME')) {
	define('PR_MHS_COMMON_NAME', 974061598);
}
if (!defined('PR_ORGANIZATIONAL_ID_NUMBER')) {
	define('PR_ORGANIZATIONAL_ID_NUMBER', 974127134);
}
if (!defined('PR_SURNAME')) {
	define('PR_SURNAME', 974192670);
}
if (!defined('PR_ORIGINAL_ENTRYID')) {
	define('PR_ORIGINAL_ENTRYID', 974258434);
}
if (!defined('PR_ORIGINAL_DISPLAY_NAME')) {
	define('PR_ORIGINAL_DISPLAY_NAME', 974323742);
}
if (!defined('PR_ORIGINAL_SEARCH_KEY')) {
	define('PR_ORIGINAL_SEARCH_KEY', 974389506);
}
if (!defined('PR_POSTAL_ADDRESS')) {
	define('PR_POSTAL_ADDRESS', 974454814);
}
if (!defined('PR_COMPANY_NAME')) {
	define('PR_COMPANY_NAME', 974520350);
}
if (!defined('PR_COMPANY_NAME_A')) {
	define('PR_COMPANY_NAME_A', 974520350);
}
if (!defined('PR_TITLE')) {
	define('PR_TITLE', 974585886);
}
if (!defined('PR_DEPARTMENT_NAME')) {
	define('PR_DEPARTMENT_NAME', 974651422);
}
if (!defined('PR_DEPARTMENT_NAME_A')) {
	define('PR_DEPARTMENT_NAME_A', 974651422);
}
if (!defined('PR_OFFICE_LOCATION')) {
	define('PR_OFFICE_LOCATION', 974716958);
}
if (!defined('PR_OFFICE_LOCATION_A')) {
	define('PR_OFFICE_LOCATION_A', 974716958);
}
if (!defined('PR_PRIMARY_TELEPHONE_NUMBER')) {
	define('PR_PRIMARY_TELEPHONE_NUMBER', 974782494);
}
if (!defined('PR_PRIMARY_TELEPHONE_NUMBER_A')) {
	define('PR_PRIMARY_TELEPHONE_NUMBER_A', 974782494);
}
if (!defined('PR_BUSINESS2_TELEPHONE_NUMBER')) {
	define('PR_BUSINESS2_TELEPHONE_NUMBER', 974848030);
}
if (!defined('PR_BUSINESS2_TELEPHONE_NUMBER_MV')) {
	define('PR_BUSINESS2_TELEPHONE_NUMBER_MV', 974852126);
}
if (!defined('PR_OFFICE2_TELEPHONE_NUMBER')) {
	define('PR_OFFICE2_TELEPHONE_NUMBER', 974848030);
}
if (!defined('PR_MOBILE_TELEPHONE_NUMBER')) {
	define('PR_MOBILE_TELEPHONE_NUMBER', 974913566);
}
if (!defined('PR_CELLULAR_TELEPHONE_NUMBER')) {
	define('PR_CELLULAR_TELEPHONE_NUMBER', 974913566);
}
if (!defined('PR_RADIO_TELEPHONE_NUMBER')) {
	define('PR_RADIO_TELEPHONE_NUMBER', 974979102);
}
if (!defined('PR_CAR_TELEPHONE_NUMBER')) {
	define('PR_CAR_TELEPHONE_NUMBER', 975044638);
}
if (!defined('PR_OTHER_TELEPHONE_NUMBER')) {
	define('PR_OTHER_TELEPHONE_NUMBER', 975110174);
}
if (!defined('PR_TRANSMITABLE_DISPLAY_NAME')) {
	define('PR_TRANSMITABLE_DISPLAY_NAME', 975175710);
}
if (!defined('PR_TRANSMITABLE_DISPLAY_NAME_A')) {
	define('PR_TRANSMITABLE_DISPLAY_NAME_A', 975175710);
}
if (!defined('PR_BEEPER_TELEPHONE_NUMBER')) {
	define('PR_BEEPER_TELEPHONE_NUMBER', 975241246);
}
if (!defined('PR_PAGER_TELEPHONE_NUMBER')) {
	define('PR_PAGER_TELEPHONE_NUMBER', 975241246);
}
if (!defined('PR_USER_CERTIFICATE')) {
	define('PR_USER_CERTIFICATE', 975307010);
}
if (!defined('PR_PRIMARY_FAX_NUMBER')) {
	define('PR_PRIMARY_FAX_NUMBER', 975372318);
}
if (!defined('PR_BUSINESS_FAX_NUMBER')) {
	define('PR_BUSINESS_FAX_NUMBER', 975437854);
}
if (!defined('PR_HOME_FAX_NUMBER')) {
	define('PR_HOME_FAX_NUMBER', 975503390);
}
if (!defined('PR_BUSINESS_ADDRESS_COUNTRY')) {
	define('PR_BUSINESS_ADDRESS_COUNTRY', 975568926);
}
if (!defined('PR_COUNTRY')) {
	define('PR_COUNTRY', 975568926);
}
if (!defined('PR_BUSINESS_ADDRESS_CITY')) {
	define('PR_BUSINESS_ADDRESS_CITY', 975634462);
}
if (!defined('PR_LOCALITY')) {
	define('PR_LOCALITY', 975634462);
}
if (!defined('PR_BUSINESS_ADDRESS_STATE_OR_PROVINCE')) {
	define('PR_BUSINESS_ADDRESS_STATE_OR_PROVINCE', 975699998);
}
if (!defined('PR_STATE_OR_PROVINCE')) {
	define('PR_STATE_OR_PROVINCE', 975699998);
}
if (!defined('PR_BUSINESS_ADDRESS_STREET')) {
	define('PR_BUSINESS_ADDRESS_STREET', 975765534);
}
if (!defined('PR_STREET_ADDRESS')) {
	define('PR_STREET_ADDRESS', 975765534);
}
if (!defined('PR_BUSINESS_ADDRESS_POSTAL_CODE')) {
	define('PR_BUSINESS_ADDRESS_POSTAL_CODE', 975831070);
}
if (!defined('PR_POSTAL_CODE')) {
	define('PR_POSTAL_CODE', 975831070);
}
if (!defined('PR_BUSINESS_ADDRESS_POST_OFFICE_BOX')) {
	define('PR_BUSINESS_ADDRESS_POST_OFFICE_BOX', 975896606);
}
if (!defined('PR_POST_OFFICE_BOX')) {
	define('PR_POST_OFFICE_BOX', 975896606);
}
if (!defined('PR_TELEX_NUMBER')) {
	define('PR_TELEX_NUMBER', 975962142);
}
if (!defined('PR_ISDN_NUMBER')) {
	define('PR_ISDN_NUMBER', 976027678);
}
if (!defined('PR_ASSISTANT_TELEPHONE_NUMBER')) {
	define('PR_ASSISTANT_TELEPHONE_NUMBER', 976093214);
}
if (!defined('PR_HOME2_TELEPHONE_NUMBER')) {
	define('PR_HOME2_TELEPHONE_NUMBER', 976158750);
}
if (!defined('PR_HOME2_TELEPHONE_NUMBER_MV')) {
	define('PR_HOME2_TELEPHONE_NUMBER_MV', 976162846);
}
if (!defined('PR_ASSISTANT')) {
	define('PR_ASSISTANT', 976224286);
}
if (!defined('PR_SEND_RICH_INFO')) {
	define('PR_SEND_RICH_INFO', 977272843);
}
if (!defined('PR_WEDDING_ANNIVERSARY')) {
	define('PR_WEDDING_ANNIVERSARY', 977338432);
}
if (!defined('PR_BIRTHDAY')) {
	define('PR_BIRTHDAY', 977403968);
}
if (!defined('PR_HOBBIES')) {
	define('PR_HOBBIES', 977469470);
}
if (!defined('PR_MIDDLE_NAME')) {
	define('PR_MIDDLE_NAME', 977535006);
}
if (!defined('PR_DISPLAY_NAME_PREFIX')) {
	define('PR_DISPLAY_NAME_PREFIX', 977600542);
}
if (!defined('PR_PROFESSION')) {
	define('PR_PROFESSION', 977666078);
}
if (!defined('PR_PREFERRED_BY_NAME')) {
	define('PR_PREFERRED_BY_NAME', 977731614);
}
if (!defined('PR_SPOUSE_NAME')) {
	define('PR_SPOUSE_NAME', 977797150);
}
if (!defined('PR_COMPUTER_NETWORK_NAME')) {
	define('PR_COMPUTER_NETWORK_NAME', 977862686);
}
if (!defined('PR_CUSTOMER_ID')) {
	define('PR_CUSTOMER_ID', 977928222);
}
if (!defined('PR_TTYTDD_PHONE_NUMBER')) {
	define('PR_TTYTDD_PHONE_NUMBER', 977993758);
}
if (!defined('PR_FTP_SITE')) {
	define('PR_FTP_SITE', 978059294);
}
if (!defined('PR_GENDER')) {
	define('PR_GENDER', 978124802);
}
if (!defined('PR_MANAGER_NAME')) {
	define('PR_MANAGER_NAME', 978190366);
}
if (!defined('PR_NICKNAME')) {
	define('PR_NICKNAME', 978255902);
}
if (!defined('PR_PERSONAL_HOME_PAGE')) {
	define('PR_PERSONAL_HOME_PAGE', 978321438);
}
if (!defined('PR_BUSINESS_HOME_PAGE')) {
	define('PR_BUSINESS_HOME_PAGE', 978386974);
}
if (!defined('PR_CONTACT_VERSION')) {
	define('PR_CONTACT_VERSION', 978452552);
}
if (!defined('PR_CONTACT_ENTRYIDS')) {
	define('PR_CONTACT_ENTRYIDS', 978522370);
}
if (!defined('PR_CONTACT_ADDRTYPES')) {
	define('PR_CONTACT_ADDRTYPES', 978587678);
}
if (!defined('PR_CONTACT_DEFAULT_ADDRESS_INDEX')) {
	define('PR_CONTACT_DEFAULT_ADDRESS_INDEX', 978649091);
}
if (!defined('PR_CONTACT_EMAIL_ADDRESSES')) {
	define('PR_CONTACT_EMAIL_ADDRESSES', 978718750);
}
if (!defined('PR_COMPANY_MAIN_PHONE_NUMBER')) {
	define('PR_COMPANY_MAIN_PHONE_NUMBER', 978780190);
}
if (!defined('PR_CHILDRENS_NAMES')) {
	define('PR_CHILDRENS_NAMES', 978849822);
}
if (!defined('PR_HOME_ADDRESS_CITY')) {
	define('PR_HOME_ADDRESS_CITY', 978911262);
}
if (!defined('PR_HOME_ADDRESS_COUNTRY')) {
	define('PR_HOME_ADDRESS_COUNTRY', 978976798);
}
if (!defined('PR_HOME_ADDRESS_POSTAL_CODE')) {
	define('PR_HOME_ADDRESS_POSTAL_CODE', 979042334);
}
if (!defined('PR_HOME_ADDRESS_STATE_OR_PROVINCE')) {
	define('PR_HOME_ADDRESS_STATE_OR_PROVINCE', 979107870);
}
if (!defined('PR_HOME_ADDRESS_STREET')) {
	define('PR_HOME_ADDRESS_STREET', 979173406);
}
if (!defined('PR_HOME_ADDRESS_POST_OFFICE_BOX')) {
	define('PR_HOME_ADDRESS_POST_OFFICE_BOX', 979238942);
}
if (!defined('PR_OTHER_ADDRESS_CITY')) {
	define('PR_OTHER_ADDRESS_CITY', 979304478);
}
if (!defined('PR_OTHER_ADDRESS_COUNTRY')) {
	define('PR_OTHER_ADDRESS_COUNTRY', 979370014);
}
if (!defined('PR_OTHER_ADDRESS_POSTAL_CODE')) {
	define('PR_OTHER_ADDRESS_POSTAL_CODE', 979435550);
}
if (!defined('PR_OTHER_ADDRESS_STATE_OR_PROVINCE')) {
	define('PR_OTHER_ADDRESS_STATE_OR_PROVINCE', 979501086);
}
if (!defined('PR_OTHER_ADDRESS_STREET')) {
	define('PR_OTHER_ADDRESS_STREET', 979566622);
}
if (!defined('PR_OTHER_ADDRESS_POST_OFFICE_BOX')) {
	define('PR_OTHER_ADDRESS_POST_OFFICE_BOX', 979632158);
}
if (!defined('PR_USER_X509_CERTIFICATE')) {
	define('PR_USER_X509_CERTIFICATE', 980422914);
}
if (!defined('PR_SEND_INTERNET_ENCODING')) {
	define('PR_SEND_INTERNET_ENCODING', 980484099);
}
if (!defined('PR_STORE_PROVIDERS')) {
	define('PR_STORE_PROVIDERS', 1023410434);
}
if (!defined('PR_AB_PROVIDERS')) {
	define('PR_AB_PROVIDERS', 1023475970);
}
if (!defined('PR_TRANSPORT_PROVIDERS')) {
	define('PR_TRANSPORT_PROVIDERS', 1023541506);
}
if (!defined('PR_DEFAULT_PROFILE')) {
	define('PR_DEFAULT_PROFILE', 1023672331);
}
if (!defined('PR_AB_SEARCH_PATH')) {
	define('PR_AB_SEARCH_PATH', 1023742210);
}
if (!defined('PR_AB_DEFAULT_DIR')) {
	define('PR_AB_DEFAULT_DIR', 1023803650);
}
if (!defined('PR_AB_DEFAULT_PAB')) {
	define('PR_AB_DEFAULT_PAB', 1023869186);
}
if (!defined('PR_FILTERING_HOOKS')) {
	define('PR_FILTERING_HOOKS', 1023934722);
}
if (!defined('PR_SERVICE_NAME')) {
	define('PR_SERVICE_NAME', 1024000030);
}
if (!defined('PR_SERVICE_DLL_NAME')) {
	define('PR_SERVICE_DLL_NAME', 1024065566);
}
if (!defined('PR_SERVICE_ENTRY_NAME')) {
	define('PR_SERVICE_ENTRY_NAME', 1024131102);
}
if (!defined('PR_SERVICE_UID')) {
	define('PR_SERVICE_UID', 1024196866);
}
if (!defined('PR_SERVICE_EXTRA_UIDS')) {
	define('PR_SERVICE_EXTRA_UIDS', 1024262402);
}
if (!defined('PR_SERVICES')) {
	define('PR_SERVICES', 1024327938);
}
if (!defined('PR_SERVICE_SUPPORT_FILES')) {
	define('PR_SERVICE_SUPPORT_FILES', 1024397342);
}
if (!defined('PR_SERVICE_DELETE_FILES')) {
	define('PR_SERVICE_DELETE_FILES', 1024462878);
}
if (!defined('PR_AB_SEARCH_PATH_UPDATE')) {
	define('PR_AB_SEARCH_PATH_UPDATE', 1024524546);
}
if (!defined('PR_PROFILE_NAME')) {
	define('PR_PROFILE_NAME', 1024589854);
}
if (!defined('PR_EMSMDB_SECTION_UID')) {
	define('PR_EMSMDB_SECTION_UID', 1024786690);
}
if (!defined('PR_EMSMDB_LEGACY')) {
	define('PR_EMSMDB_LEGACY', 1024983051);
}
if (!defined('PR_EMSABP_USER_UID')) {
	define('PR_EMSABP_USER_UID', 1025114370);
}
if (!defined('PR_AB_CHOOSE_DIRECTORY_AUTOMATICALLY')) {
	define('PR_AB_CHOOSE_DIRECTORY_AUTOMATICALLY', 1025245195);
}
if (!defined('PR_IDENTITY_DISPLAY')) {
	define('PR_IDENTITY_DISPLAY', 1040187422);
}
if (!defined('PR_IDENTITY_ENTRYID')) {
	define('PR_IDENTITY_ENTRYID', 1040253186);
}
if (!defined('PR_RESOURCE_METHODS')) {
	define('PR_RESOURCE_METHODS', 1040318467);
}
if (!defined('PR_RESOURCE_TYPE')) {
	define('PR_RESOURCE_TYPE', 1040384003);
}
if (!defined('PR_STATUS_CODE')) {
	define('PR_STATUS_CODE', 1040449539);
}
if (!defined('PR_IDENTITY_SEARCH_KEY')) {
	define('PR_IDENTITY_SEARCH_KEY', 1040515330);
}
if (!defined('PR_OWN_STORE_ENTRYID')) {
	define('PR_OWN_STORE_ENTRYID', 1040580866);
}
if (!defined('PR_RESOURCE_PATH')) {
	define('PR_RESOURCE_PATH', 1040646174);
}
if (!defined('PR_STATUS_STRING')) {
	define('PR_STATUS_STRING', 1040711710);
}
if (!defined('PR_X400_DEFERRED_DELIVERY_CANCEL')) {
	define('PR_X400_DEFERRED_DELIVERY_CANCEL', 1040777227);
}
if (!defined('PR_HEADER_FOLDER_ENTRYID')) {
	define('PR_HEADER_FOLDER_ENTRYID', 1040843010);
}
if (!defined('PR_REMOTE_PROGRESS')) {
	define('PR_REMOTE_PROGRESS', 1040908291);
}
if (!defined('PR_REMOTE_PROGRESS_TEXT')) {
	define('PR_REMOTE_PROGRESS_TEXT', 1040973854);
}
if (!defined('PR_REMOTE_VALIDATE_OK')) {
	define('PR_REMOTE_VALIDATE_OK', 1041039371);
}
if (!defined('PR_CONTROL_FLAGS')) {
	define('PR_CONTROL_FLAGS', 1056964611);
}
if (!defined('PR_CONTROL_STRUCTURE')) {
	define('PR_CONTROL_STRUCTURE', 1057030402);
}
if (!defined('PR_CONTROL_TYPE')) {
	define('PR_CONTROL_TYPE', 1057095683);
}
if (!defined('PR_DELTAX')) {
	define('PR_DELTAX', 1057161219);
}
if (!defined('PR_DELTAY')) {
	define('PR_DELTAY', 1057226755);
}
if (!defined('PR_XPOS')) {
	define('PR_XPOS', 1057292291);
}
if (!defined('PR_YPOS')) {
	define('PR_YPOS', 1057357827);
}
if (!defined('PR_CONTROL_ID')) {
	define('PR_CONTROL_ID', 1057423618);
}
if (!defined('PR_INITIAL_DETAILS_PANE')) {
	define('PR_INITIAL_DETAILS_PANE', 1057488899);
}
if (!defined('PR_PREVIEW_UNREAD')) {
	define('PR_PREVIEW_UNREAD', 1071120414);
}
if (!defined('PR_PREVIEW')) {
	define('PR_PREVIEW', 1071185950);
}
if (!defined('PR_INTERNET_CPID')) {
	define('PR_INTERNET_CPID', 1071513603);
}
if (!defined('PR_AUTO_RESPONSE_SUPPRESS')) {
	define('PR_AUTO_RESPONSE_SUPPRESS', 1071579139);
}
if (!defined('PR_ACL_DATA')) {
	define('PR_ACL_DATA', 1071644930);
}
if (!defined('PR_ACL_TABLE')) {
	define('PR_ACL_TABLE', 1071644685);
}
if (!defined('PR_RULES_DATA')) {
	define('PR_RULES_DATA', 1071710466);
}
if (!defined('PR_RULES_TABLE')) {
	define('PR_RULES_TABLE', 1071710221);
}
if (!defined('PR_DELEGATED_BY_RULE')) {
	define('PR_DELEGATED_BY_RULE', 1071841291);
}
if (!defined('PR_RESOLVE_METHOD')) {
	define('PR_RESOLVE_METHOD', 1072103427);
}
if (!defined('PR_HAS_DAMS')) {
	define('PR_HAS_DAMS', 1072300043);
}
if (!defined('PR_DEFERRED_SEND_NUMBER')) {
	define('PR_DEFERRED_SEND_NUMBER', 1072365571);
}
if (!defined('PR_DEFERRED_SEND_UNITS')) {
	define('PR_DEFERRED_SEND_UNITS', 1072431107);
}
if (!defined('PR_EXPIRY_NUMBER')) {
	define('PR_EXPIRY_NUMBER', 1072496643);
}
if (!defined('PR_EXPIRY_UNITS')) {
	define('PR_EXPIRY_UNITS', 1072562179);
}
if (!defined('PR_DEFERRED_SEND_TIME')) {
	define('PR_DEFERRED_SEND_TIME', 1072627776);
}
if (!defined('PR_CONFLICT_ENTRYID')) {
	define('PR_CONFLICT_ENTRYID', 1072693506);
}
if (!defined('PR_MESSAGE_LOCALE_ID')) {
	define('PR_MESSAGE_LOCALE_ID', 1072758787);
}
if (!defined('PR_STORAGE_QUOTA_LIMIT')) {
	define('PR_STORAGE_QUOTA_LIMIT', 1073020931);
}
if (!defined('PR_CREATOR_NAME')) {
	define('PR_CREATOR_NAME', 1073217566);
}
if (!defined('PR_CREATOR_ENTRYID')) {
	define('PR_CREATOR_ENTRYID', 1073283330);
}
if (!defined('PR_LAST_MODIFIER_NAME')) {
	define('PR_LAST_MODIFIER_NAME', 1073348638);
}
if (!defined('PR_LAST_MODIFIER_ENTRYID')) {
	define('PR_LAST_MODIFIER_ENTRYID', 1073414402);
}
if (!defined('PR_MESSAGE_CODEPAGE')) {
	define('PR_MESSAGE_CODEPAGE', 1073545219);
}
if (!defined('PR_SENT_REPRESENTING_FLAGS')) {
	define('PR_SENT_REPRESENTING_FLAGS', 1075445763);
}
if (!defined('PR_CONTENT_FILTER_SCL')) {
	define('PR_CONTENT_FILTER_SCL', 1081475075);
}
if (!defined('PR_SENDER_ID_STATUS')) {
	define('PR_SENDER_ID_STATUS', 1081671683);
}
if (!defined('PR_HIER_REV')) {
	define('PR_HIER_REV', 1082261568);
}
if (!defined('PR_PURPORTED_SENDER_DOMAIN')) {
	define('PR_PURPORTED_SENDER_DOMAIN', 1082327070);
}
if (!defined('PR_PURPORTED_SENDER_DOMAIN_A')) {
	define('PR_PURPORTED_SENDER_DOMAIN_A', 1082327070);
}
if (!defined('PR_INETMAIL_OVERRIDE_FORMAT')) {
	define('PR_INETMAIL_OVERRIDE_FORMAT', 1493303299);
}
if (!defined('PR_MSG_EDITOR_FORMAT')) {
	define('PR_MSG_EDITOR_FORMAT', 1493762051);
}
if (!defined('PR_SENDER_SMTP_ADDRESS')) {
	define('PR_SENDER_SMTP_ADDRESS', 1560346654);
}
if (!defined('PR_SENT_REPRESENTING_SMTP_ADDRESS')) {
	define('PR_SENT_REPRESENTING_SMTP_ADDRESS', 1560412190);
}
if (!defined('PR_RECIPIENT_ORDER')) {
	define('PR_RECIPIENT_ORDER', 1608450051);
}
if (!defined('PR_RECIPIENT_PROPOSED')) {
	define('PR_RECIPIENT_PROPOSED', 1608581131);
}
if (!defined('PR_RECIPIENT_PROPOSEDSTARTTIME')) {
	define('PR_RECIPIENT_PROPOSEDSTARTTIME', 1608712256);
}
if (!defined('PR_RECIPIENT_PROPOSEDENDTIME')) {
	define('PR_RECIPIENT_PROPOSEDENDTIME', 1608777792);
}
if (!defined('PR_RECIPIENT_DISPLAY_NAME')) {
	define('PR_RECIPIENT_DISPLAY_NAME', 1609957406);
}
if (!defined('PR_RECIPIENT_ENTRYID')) {
	define('PR_RECIPIENT_ENTRYID', 1610023170);
}
if (!defined('PR_RECIPIENT_TRACKSTATUS_TIME')) {
	define('PR_RECIPIENT_TRACKSTATUS_TIME', 1610285120);
}
if (!defined('PR_RECIPIENT_FLAGS')) {
	define('PR_RECIPIENT_FLAGS', 1610416131);
}
if (!defined('PR_RECIPIENT_TRACKSTATUS')) {
	define('PR_RECIPIENT_TRACKSTATUS', 1610547203);
}
if (!defined('PR_JUNK_INCLUDE_CONTACTS')) {
	define('PR_JUNK_INCLUDE_CONTACTS', 1627389955);
}
if (!defined('PR_JUNK_THRESHOLD')) {
	define('PR_JUNK_THRESHOLD', 1627455491);
}
if (!defined('PR_JUNK_PERMANENTLY_DELETE')) {
	define('PR_JUNK_PERMANENTLY_DELETE', 1627521027);
}
if (!defined('PR_JUNK_ADD_RECIPS_TO_SSL')) {
	define('PR_JUNK_ADD_RECIPS_TO_SSL', 1627586563);
}
if (!defined('PR_JUNK_PHISHING_ENABLE_LINKS')) {
	define('PR_JUNK_PHISHING_ENABLE_LINKS', 1627848715);
}
if (!defined('PR_REPLY_TEMPLATE_ID')) {
	define('PR_REPLY_TEMPLATE_ID', 1707213058);
}
if (!defined('PR_SECURE_SUBMIT_FLAGS')) {
	define('PR_SECURE_SUBMIT_FLAGS', 1707474947);
}
if (!defined('PR_SOURCE_KEY')) {
	define('PR_SOURCE_KEY', 1709179138);
}
if (!defined('PR_PARENT_SOURCE_KEY')) {
	define('PR_PARENT_SOURCE_KEY', 1709244674);
}
if (!defined('PR_CHANGE_KEY')) {
	define('PR_CHANGE_KEY', 1709310210);
}
if (!defined('PR_PREDECESSOR_CHANGE_LIST')) {
	define('PR_PREDECESSOR_CHANGE_LIST', 1709375746);
}
if (!defined('PR_RULE_MSG_STATE')) {
	define('PR_RULE_MSG_STATE', 1709768707);
}
if (!defined('PR_RULE_MSG_USER_FLAGS')) {
	define('PR_RULE_MSG_USER_FLAGS', 1709834243);
}
if (!defined('PR_RULE_MSG_PROVIDER')) {
	define('PR_RULE_MSG_PROVIDER', 1709899806);
}
if (!defined('PR_RULE_MSG_NAME')) {
	define('PR_RULE_MSG_NAME', 1709965342);
}
if (!defined('PR_RULE_MSG_LEVEL')) {
	define('PR_RULE_MSG_LEVEL', 1710030851);
}
if (!defined('PR_RULE_MSG_PROVIDER_DATA')) {
	define('PR_RULE_MSG_PROVIDER_DATA', 1710096642);
}
if (!defined('PR_RULE_MSG_SEQUENCE')) {
	define('PR_RULE_MSG_SEQUENCE', 1710424067);
}
if (!defined('PR_PST_RECEIVE_FOLDER_NID')) {
	define('PR_PST_RECEIVE_FOLDER_NID', 1711603715);
}
if (!defined('PR_PROFILE_TRANSPORT_FLAGS')) {
	define('PR_PROFILE_TRANSPORT_FLAGS', 1711603715);
}
if (!defined('PR_USER_ENTRYID')) {
	define('PR_USER_ENTRYID', 1712914690);
}
if (!defined('PR_USER_NAME')) {
	define('PR_USER_NAME', 1712979998);
}
if (!defined('PR_MAILBOX_OWNER_ENTRYID')) {
	define('PR_MAILBOX_OWNER_ENTRYID', 1713045762);
}
if (!defined('PR_MAILBOX_OWNER_NAME')) {
	define('PR_MAILBOX_OWNER_NAME', 1713111070);
}
if (!defined('PR_MAILBOX_OWNER_NAME_A')) {
	define('PR_MAILBOX_OWNER_NAME_A', 1713111070);
}
if (!defined('PR_OOF_STATE')) {
	define('PR_OOF_STATE', 1713176587);
}
if (!defined('PR_SCHEDULE_FOLDER_ENTRYID')) {
	define('PR_SCHEDULE_FOLDER_ENTRYID', 1713242370);
}
if (!defined('PR_IPM_DAF_ENTRYID')) {
	define('PR_IPM_DAF_ENTRYID', 1713307906);
}
if (!defined('PR_NON_IPM_SUBTREE_ENTRYID')) {
	define('PR_NON_IPM_SUBTREE_ENTRYID', 1713373442);
}
if (!defined('PR_EFORMS_REGISTRY_ENTRYID')) {
	define('PR_EFORMS_REGISTRY_ENTRYID', 1713438978);
}
if (!defined('PR_SPLUS_FREE_BUSY_ENTRYID')) {
	define('PR_SPLUS_FREE_BUSY_ENTRYID', 1713504514);
}
if (!defined('PR_OFFLINE_ADDRBOOK_ENTRYID')) {
	define('PR_OFFLINE_ADDRBOOK_ENTRYID', 1713570050);
}
if (!defined('PR_TEST_LINE_SPEED')) {
	define('PR_TEST_LINE_SPEED', 1714094338);
}
if (!defined('PR_HIERARCHY_SYNCHRONIZER')) {
	define('PR_HIERARCHY_SYNCHRONIZER', 1714159629);
}
if (!defined('PR_CONTENTS_SYNCHRONIZER')) {
	define('PR_CONTENTS_SYNCHRONIZER', 1714225165);
}
if (!defined('PR_COLLECTOR')) {
	define('PR_COLLECTOR', 1714290701);
}
if (!defined('PR_IPM_FAVORITES_ENTRYID')) {
	define('PR_IPM_FAVORITES_ENTRYID', 1714422018);
}
if (!defined('PR_IPM_PUBLIC_FOLDERS_ENTRYID')) {
	define('PR_IPM_PUBLIC_FOLDERS_ENTRYID', 1714487554);
}
if (!defined('PR_STORE_OFFLINE')) {
	define('PR_STORE_OFFLINE', 1714552843);
}
if (!defined('PR_HIERARCHY_SERVER')) {
	define('PR_HIERARCHY_SERVER', 1714618398);
}
if (!defined('PR_PST_LRNORESTRICTIONS')) {
	define('PR_PST_LRNORESTRICTIONS', 1714618379);
}
if (!defined('PR_FAVORITES_DEFAULT_NAME')) {
	define('PR_FAVORITES_DEFAULT_NAME', 1714749470);
}
if (!defined('PR_PROFILE_OAB_COUNT_ATTEMPTED_FULLDN')) {
	define('PR_PROFILE_OAB_COUNT_ATTEMPTED_FULLDN', 1714749443);
}
if (!defined('PR_PST_HIDDEN_COUNT')) {
	define('PR_PST_HIDDEN_COUNT', 1714749443);
}
if (!defined('PR_PROFILE_OAB_COUNT_ATTEMPTED_INCRDN')) {
	define('PR_PROFILE_OAB_COUNT_ATTEMPTED_INCRDN', 1714814979);
}
if (!defined('PR_PST_HIDDEN_UNREAD')) {
	define('PR_PST_HIDDEN_UNREAD', 1714814979);
}
if (!defined('PR_FOLDER_CHILD_COUNT')) {
	define('PR_FOLDER_CHILD_COUNT', 1714946051);
}
if (!defined('PR_RIGHTS')) {
	define('PR_RIGHTS', 1715011587);
}
if (!defined('PR_HAS_RULES')) {
	define('PR_HAS_RULES', 1715077131);
}
if (!defined('PR_ADDRESS_BOOK_ENTRYID')) {
	define('PR_ADDRESS_BOOK_ENTRYID', 1715142914);
}
if (!defined('PR_HIERARCHY_CHANGE_NUM')) {
	define('PR_HIERARCHY_CHANGE_NUM', 1715339267);
}
if (!defined('PR_DELETED_MSG_COUNT')) {
	define('PR_DELETED_MSG_COUNT', 1715470339);
}
if (!defined('PR_DELETED_FOLDER_COUNT')) {
	define('PR_DELETED_FOLDER_COUNT', 1715535875);
}
if (!defined('PR_DELETED_ASSOC_MSG_COUNT')) {
	define('PR_DELETED_ASSOC_MSG_COUNT', 1715666947);
}
if (!defined('PR_REPLICA_SERVER')) {
	define('PR_REPLICA_SERVER', 1715732510);
}
if (!defined('PR_CLIENT_ACTIONS')) {
	define('PR_CLIENT_ACTIONS', 1715798274);
}
if (!defined('PR_DAM_ORIGINAL_ENTRYID')) {
	define('PR_DAM_ORIGINAL_ENTRYID', 1715863810);
}
if (!defined('PR_DAM_BACK_PATCHED')) {
	define('PR_DAM_BACK_PATCHED', 1715929099);
}
if (!defined('PR_RULE_ERROR')) {
	define('PR_RULE_ERROR', 1715994627);
}
if (!defined('PR_RULE_ACTION_TYPE')) {
	define('PR_RULE_ACTION_TYPE', 1716060163);
}
if (!defined('PR_HAS_NAMED_PROPERTIES')) {
	define('PR_HAS_NAMED_PROPERTIES', 1716125707);
}
if (!defined('PR_RULE_ACTION_NUMBER')) {
	define('PR_RULE_ACTION_NUMBER', 1716518915);
}
if (!defined('PR_RULE_FOLDER_ENTRYID')) {
	define('PR_RULE_FOLDER_ENTRYID', 1716584706);
}
if (!defined('PR_PROHIBIT_RECEIVE_QUOTA')) {
	define('PR_PROHIBIT_RECEIVE_QUOTA', 1718222851);
}
if (!defined('PR_IN_CONFLICT')) {
	define('PR_IN_CONFLICT', 1718353931);
}
if (!defined('PR_MAX_SUBMIT_MESSAGE_SIZE')) {
	define('PR_MAX_SUBMIT_MESSAGE_SIZE', 1718419459);
}
if (!defined('PR_PROHIBIT_SEND_QUOTA')) {
	define('PR_PROHIBIT_SEND_QUOTA', 1718484995);
}
if (!defined('PR_EC_USER_LANGUAGE')) {
	define('PR_EC_USER_LANGUAGE', 1718616094);
}
if (!defined('PR_EC_USER_TIMEZONE')) {
	define('PR_EC_USER_TIMEZONE', 1718681630);
}
if (!defined('PR_MEMBER_ID')) {
	define('PR_MEMBER_ID', 1718681620);
}
if (!defined('PR_MEMBER_NAME')) {
	define('PR_MEMBER_NAME', 1718747166);
}
if (!defined('PR_MEMBER_NAME_A')) {
	define('PR_MEMBER_NAME_A', 1718747166);
}
if (!defined('PR_MEMBER_RIGHTS')) {
	define('PR_MEMBER_RIGHTS', 1718812675);
}
if (!defined('PR_RULE_ID')) {
	define('PR_RULE_ID', 1718878228);
}
if (!defined('PR_RULE_IDS')) {
	define('PR_RULE_IDS', 1718944002);
}
if (!defined('PR_RULE_SEQUENCE')) {
	define('PR_RULE_SEQUENCE', 1719009283);
}
if (!defined('PR_RULE_STATE')) {
	define('PR_RULE_STATE', 1719074819);
}
if (!defined('PR_RULE_USER_FLAGS')) {
	define('PR_RULE_USER_FLAGS', 1719140355);
}
if (!defined('PR_RULE_CONDITION')) {
	define('PR_RULE_CONDITION', 1719206141);
}
if (!defined('PR_RULE_ACTIONS')) {
	define('PR_RULE_ACTIONS', 1719664894);
}
if (!defined('PR_RULE_PROVIDER')) {
	define('PR_RULE_PROVIDER', 1719730206);
}
if (!defined('PR_RULE_PROVIDER_A')) {
	define('PR_RULE_PROVIDER_A', 1719730206);
}
if (!defined('PR_RULE_NAME')) {
	define('PR_RULE_NAME', 1719795742);
}
if (!defined('PR_RULE_NAME_A')) {
	define('PR_RULE_NAME_A', 1719795742);
}
if (!defined('PR_RULE_LEVEL')) {
	define('PR_RULE_LEVEL', 1719861251);
}
if (!defined('PR_RULE_PROVIDER_DATA')) {
	define('PR_RULE_PROVIDER_DATA', 1719927042);
}
if (!defined('PR_RULE_VERSION')) {
	define('PR_RULE_VERSION', 1720516610);
}
if (!defined('PR_DELETED_ON')) {
	define('PR_DELETED_ON', 1720647744);
}
if (!defined('PR_DELETED_MESSAGE_SIZE')) {
	define('PR_DELETED_MESSAGE_SIZE', 1721434115);
}
if (!defined('PR_DELETED_MESSAGE_SIZE_EXTENDED')) {
	define('PR_DELETED_MESSAGE_SIZE_EXTENDED', 1721434132);
}
if (!defined('PR_DELETED_NORMAL_MESSAGE_SIZE')) {
	define('PR_DELETED_NORMAL_MESSAGE_SIZE', 1721499651);
}
if (!defined('PR_DELETED_NORMAL_MESSAGE_SIZE_EXTENDED')) {
	define('PR_DELETED_NORMAL_MESSAGE_SIZE_EXTENDED', 1721499668);
}
if (!defined('PR_DELETED_ASSOC_MESSAGE_SIZE')) {
	define('PR_DELETED_ASSOC_MESSAGE_SIZE', 1721565187);
}
if (!defined('PR_DELETED_ASSOC_MESSAGE_SIZE_EXTENDED')) {
	define('PR_DELETED_ASSOC_MESSAGE_SIZE_EXTENDED', 1721565204);
}
if (!defined('PR_LOCALE_ID')) {
	define('PR_LOCALE_ID', 1721827331);
}
if (!defined('PR_FOLDER_FLAGS')) {
	define('PR_FOLDER_FLAGS', 1722286083);
}
if (!defined('PR_NORMAL_MSG_W_ATTACH_COUNT')) {
	define('PR_NORMAL_MSG_W_ATTACH_COUNT', 1722613763);
}
if (!defined('PR_ASSOC_MSG_W_ATTACH_COUNT')) {
	define('PR_ASSOC_MSG_W_ATTACH_COUNT', 1722679299);
}
if (!defined('PR_NORMAL_MESSAGE_SIZE')) {
	define('PR_NORMAL_MESSAGE_SIZE', 1723006979);
}
if (!defined('PR_NORMAL_MESSAGE_SIZE_EXTENDED')) {
	define('PR_NORMAL_MESSAGE_SIZE_EXTENDED', 1723006996);
}
if (!defined('PR_ASSOC_MESSAGE_SIZE')) {
	define('PR_ASSOC_MESSAGE_SIZE', 1723072515);
}
if (!defined('PR_ASSOC_MESSAGE_SIZE_EXTENDED')) {
	define('PR_ASSOC_MESSAGE_SIZE_EXTENDED', 1723072532);
}
if (!defined('PR_FOLDER_PATHNAME')) {
	define('PR_FOLDER_PATHNAME', 1723138078);
}
if (!defined('PR_CODE_PAGE_ID')) {
	define('PR_CODE_PAGE_ID', 1724055555);
}
if (!defined('PR_LATEST_PST_ENSURE')) {
	define('PR_LATEST_PST_ENSURE', 1727660035);
}
if (!defined('PR_EMS_AB_MANAGE_DL')) {
	define('PR_EMS_AB_MANAGE_DL', 1728315405);
}
if (!defined('PR_SORT_LOCALE_ID')) {
	define('PR_SORT_LOCALE_ID', 1728380931);
}
if (!defined('PR_EC_SSLKEY_PASS')) {
	define('PR_EC_SSLKEY_PASS', 1728446494);
}
if (!defined('PR_LOCAL_COMMIT_TIME')) {
	define('PR_LOCAL_COMMIT_TIME', 1728643136);
}
if (!defined('PR_LOCAL_COMMIT_TIME_MAX')) {
	define('PR_LOCAL_COMMIT_TIME_MAX', 1728708672);
}
if (!defined('PR_DELETED_COUNT_TOTAL')) {
	define('PR_DELETED_COUNT_TOTAL', 1728774147);
}
if (!defined('PR_FLAT_URL_NAME')) {
	define('PR_FLAT_URL_NAME', 1728970782);
}
if (!defined('PR_ZC_CONTACT_STORE_ENTRYIDS')) {
	define('PR_ZC_CONTACT_STORE_ENTRYIDS', 1729171714);
}
if (!defined('PR_ZC_CONTACT_FOLDER_ENTRYIDS')) {
	define('PR_ZC_CONTACT_FOLDER_ENTRYIDS', 1729237250);
}
if (!defined('PR_ZC_CONTACT_FOLDER_NAMES')) {
	define('PR_ZC_CONTACT_FOLDER_NAMES', 1729302558);
}
if (!defined('PR_EC_SERVER_VERSION')) {
	define('PR_EC_SERVER_VERSION', 1729495070);
}
if (!defined('PR_QUOTA_WARNING_THRESHOLD')) {
	define('PR_QUOTA_WARNING_THRESHOLD', 1730215939);
}
if (!defined('PR_QUOTA_SEND_THRESHOLD')) {
	define('PR_QUOTA_SEND_THRESHOLD', 1730281475);
}
if (!defined('PR_QUOTA_RECEIVE_THRESHOLD')) {
	define('PR_QUOTA_RECEIVE_THRESHOLD', 1730347011);
}
if (!defined('PR_EC_MESSAGE_BCC_ME')) {
	define('PR_EC_MESSAGE_BCC_ME', 1730478091);
}
if (!defined('PR_MANAGED_FOLDER_INFORMATION')) {
	define('PR_MANAGED_FOLDER_INFORMATION', 1731002371);
}
if (!defined('PR_MANAGED_FOLDER_SIZE')) {
	define('PR_MANAGED_FOLDER_SIZE', 1731198979);
}
if (!defined('PR_MANAGED_FOLDER_STORAGE_QUOTA')) {
	define('PR_MANAGED_FOLDER_STORAGE_QUOTA', 1731264515);
}
if (!defined('PR_MANAGED_FOLDER_ID')) {
	define('PR_MANAGED_FOLDER_ID', 1731330078);
}
if (!defined('PR_MANAGED_FOLDER_COMMENT')) {
	define('PR_MANAGED_FOLDER_COMMENT', 1731395614);
}
if (!defined('PR_DAM_ORIG_MSG_SVREID')) {
	define('PR_DAM_ORIG_MSG_SVREID', 1732313346);
}
if (!defined('PR_RULE_FOLDER_FID')) {
	define('PR_RULE_FOLDER_FID', 1732378644);
}
if (!defined('PR_INTERNET_ARTICLE_NUMBER_NEXT')) {
	define('PR_INTERNET_ARTICLE_NUMBER_NEXT', 1733361667);
}
if (!defined('PR_EC_OUTOFOFFICE')) {
	define('PR_EC_OUTOFOFFICE', 1734344707);
}
if (!defined('PR_EC_OUTOFOFFICE_MSG')) {
	define('PR_EC_OUTOFOFFICE_MSG', 1734410270);
}
if (!defined('PR_EC_OUTOFOFFICE_SUBJECT')) {
	define('PR_EC_OUTOFOFFICE_SUBJECT', 1734475806);
}
if (!defined('PR_EC_OUTOFOFFICE_FROM')) {
	define('PR_EC_OUTOFOFFICE_FROM', 1734541376);
}
if (!defined('PR_EC_OUTOFOFFICE_UNTIL')) {
	define('PR_EC_OUTOFOFFICE_UNTIL', 1734606912);
}
if (!defined('PR_EC_ALLOW_EXTERNAL')) {
	define('PR_EC_ALLOW_EXTERNAL', 1734672395);
}
if (!defined('PR_EC_EXTERNAL_AUDIENCE')) {
	define('PR_EC_EXTERNAL_AUDIENCE', 1734737931);
}
if (!defined('PR_EC_EXTERNAL_REPLY')) {
	define('PR_EC_EXTERNAL_REPLY', 1734803486);
}
if (!defined('PR_EC_EXTERNAL_SUBJECT')) {
	define('PR_EC_EXTERNAL_SUBJECT', 1734869022);
}
if (!defined('PR_EC_WEBACCESS_SETTINGS_JSON')) {
	define('PR_EC_WEBACCESS_SETTINGS_JSON', 1735524382);
}
if (!defined('PR_EC_WEBAPP_PERSISTENT_SETTINGS_JSON')) {
	define('PR_EC_WEBAPP_PERSISTENT_SETTINGS_JSON', 1735655454);
}
if (!defined('PR_EC_OUTGOING_FLAGS')) {
	define('PR_EC_OUTGOING_FLAGS', 1736441859);
}
if (!defined('PR_EC_IMAP_ID')) {
	define('PR_EC_IMAP_ID', 1736572931);
}
if (!defined('PR_EC_IMAP_SUBSCRIBED')) {
	define('PR_EC_IMAP_SUBSCRIBED', 1736704258);
}
if (!defined('PR_EC_IMAP_MAX_ID')) {
	define('PR_EC_IMAP_MAX_ID', 1736769539);
}
if (!defined('PR_EC_CLIENT_SUBMIT_DATE')) {
	define('PR_EC_CLIENT_SUBMIT_DATE', 1736835136);
}
if (!defined('PR_EC_MESSAGE_DELIVERY_DATE')) {
	define('PR_EC_MESSAGE_DELIVERY_DATE', 1736900672);
}
if (!defined('PR_EC_IMAP_EMAIL_SIZE')) {
	define('PR_EC_IMAP_EMAIL_SIZE', 1737293827);
}
if (!defined('PR_EC_IMAP_BODY')) {
	define('PR_EC_IMAP_BODY', 1737359390);
}
if (!defined('PR_EC_IMAP_BODYSTRUCTURE')) {
	define('PR_EC_IMAP_BODYSTRUCTURE', 1737424926);
}
if (!defined('PR_ASSOCIATED')) {
	define('PR_ASSOCIATED', 1739194379);
}
if (!defined('PR_EC_ENABLED_FEATURES_L')) {
	define('PR_EC_ENABLED_FEATURES_L', 1739784195);
}
if (!defined('PR_LTP_ROW_ID')) {
	define('PR_LTP_ROW_ID', 1743912963);
}
if (!defined('PR_LTP_ROW_VER')) {
	define('PR_LTP_ROW_VER', 1743978499);
}
if (!defined('PR_PST_PASSWORD')) {
	define('PR_PST_PASSWORD', 1744764931);
}
if (!defined('PR_OAB_NAME')) {
	define('PR_OAB_NAME', 1744830494);
}
if (!defined('PR_OAB_SEQUENCE')) {
	define('PR_OAB_SEQUENCE', 1744896003);
}
if (!defined('PR_RW_RULES_STREAM')) {
	define('PR_RW_RULES_STREAM', 1744961794);
}
if (!defined('PR_SENDER_TELEPHONE_NUMBER')) {
	define('PR_SENDER_TELEPHONE_NUMBER', 1744961566);
}
if (!defined('PR_SENDER_TELEPHONE_NUMBER_A')) {
	define('PR_SENDER_TELEPHONE_NUMBER_A', 1744961566);
}
if (!defined('PR_OAB_CONTAINER_GUID')) {
	define('PR_OAB_CONTAINER_GUID', 1744961566);
}
if (!defined('PR_OAB_MESSAGE_CLASS')) {
	define('PR_OAB_MESSAGE_CLASS', 1745027075);
}
if (!defined('PR_OAB_DN')) {
	define('PR_OAB_DN', 1745092638);
}
if (!defined('PR_OAB_TRUNCATED_PROPS')) {
	define('PR_OAB_TRUNCATED_PROPS', 1745162243);
}
if (!defined('PR_WB_SF_LAST_USED')) {
	define('PR_WB_SF_LAST_USED', 1748238339);
}
if (!defined('PR_WB_SF_EXPIRATION')) {
	define('PR_WB_SF_EXPIRATION', 1748631555);
}
if (!defined('PR_WB_SF_TEMPLATE_ID')) {
	define('PR_WB_SF_TEMPLATE_ID', 1749090307);
}
if (!defined('PR_SCHDINFO_BOSS_WANTS_COPY')) {
	define('PR_SCHDINFO_BOSS_WANTS_COPY', 1749155851);
}
if (!defined('PR_WB_SF_ID')) {
	define('PR_WB_SF_ID', 1749156098);
}
if (!defined('PR_SCHDINFO_DONT_MAIL_DELEGATES')) {
	define('PR_SCHDINFO_DONT_MAIL_DELEGATES', 1749221387);
}
if (!defined('PR_SCHDINFO_DELEGATE_NAMES')) {
	define('PR_SCHDINFO_DELEGATE_NAMES', 1749291038);
}
if (!defined('PR_WB_SF_RECREATE_INFO')) {
	define('PR_WB_SF_RECREATE_INFO', 1749287170);
}
if (!defined('PR_SCHDINFO_DELEGATE_ENTRYIDS')) {
	define('PR_SCHDINFO_DELEGATE_ENTRYIDS', 1749356802);
}
if (!defined('PR_WB_SF_DEFINITION')) {
	define('PR_WB_SF_DEFINITION', 1749352706);
}
if (!defined('PR_GATEWAY_NEEDS_TO_REFRESH')) {
	define('PR_GATEWAY_NEEDS_TO_REFRESH', 1749417995);
}
if (!defined('PR_WB_SF_STORAGE_TYPE')) {
	define('PR_WB_SF_STORAGE_TYPE', 1749417987);
}
if (!defined('PR_FREEBUSY_PUBLISH_START')) {
	define('PR_FREEBUSY_PUBLISH_START', 1749483523);
}
if (!defined('PR_FREEBUSY_PUBLISH_END')) {
	define('PR_FREEBUSY_PUBLISH_END', 1749549059);
}
if (!defined('PR_FREEBUSY_EMA')) {
	define('PR_FREEBUSY_EMA', 1749614622);
}
if (!defined('PR_WLINK_TYPE')) {
	define('PR_WLINK_TYPE', 1749614595);
}
if (!defined('PR_SCHDINFO_DELEGATE_NAMES_W')) {
	define('PR_SCHDINFO_DELEGATE_NAMES_W', 1749684254);
}
if (!defined('PR_WLINK_FLAGS')) {
	define('PR_WLINK_FLAGS', 1749680131);
}
if (!defined('PR_SCHDINFO_BOSS_WANTS_INFO')) {
	define('PR_SCHDINFO_BOSS_WANTS_INFO', 1749745675);
}
if (!defined('PR_WLINK_ORDINAL')) {
	define('PR_WLINK_ORDINAL', 1749745922);
}
if (!defined('PR_WLINK_ENTRYID')) {
	define('PR_WLINK_ENTRYID', 1749811458);
}
if (!defined('PR_WLINK_RECKEY')) {
	define('PR_WLINK_RECKEY', 1749876994);
}
if (!defined('PR_WLINK_STORE_ENTRYID')) {
	define('PR_WLINK_STORE_ENTRYID', 1749942530);
}
if (!defined('PR_SCHDINFO_MONTHS_MERGED')) {
	define('PR_SCHDINFO_MONTHS_MERGED', 1750011907);
}
if (!defined('PR_WLINK_FOLDER_TYPE')) {
	define('PR_WLINK_FOLDER_TYPE', 1750008066);
}
if (!defined('PR_SCHDINFO_FREEBUSY_MERGED')) {
	define('PR_SCHDINFO_FREEBUSY_MERGED', 1750077698);
}
if (!defined('PR_WLINK_GROUP_CLSID')) {
	define('PR_WLINK_GROUP_CLSID', 1750073602);
}
if (!defined('PR_SCHDINFO_MONTHS_TENTATIVE')) {
	define('PR_SCHDINFO_MONTHS_TENTATIVE', 1750142979);
}
if (!defined('PR_WLINK_GROUP_NAME')) {
	define('PR_WLINK_GROUP_NAME', 1750138910);
}
if (!defined('PR_SCHDINFO_FREEBUSY_TENTATIVE')) {
	define('PR_SCHDINFO_FREEBUSY_TENTATIVE', 1750208770);
}
if (!defined('PR_WLINK_SECTION')) {
	define('PR_WLINK_SECTION', 1750204419);
}
if (!defined('PR_SCHDINFO_MONTHS_BUSY')) {
	define('PR_SCHDINFO_MONTHS_BUSY', 1750274051);
}
if (!defined('PR_WLINK_CALENDAR_COLOR')) {
	define('PR_WLINK_CALENDAR_COLOR', 1750269955);
}
if (!defined('PR_SCHDINFO_FREEBUSY_BUSY')) {
	define('PR_SCHDINFO_FREEBUSY_BUSY', 1750339842);
}
if (!defined('PR_WLINK_ABEID')) {
	define('PR_WLINK_ABEID', 1750335746);
}
if (!defined('PR_SCHDINFO_MONTHS_OOF')) {
	define('PR_SCHDINFO_MONTHS_OOF', 1750405123);
}
if (!defined('PR_SCHDINFO_FREEBUSY_OOF')) {
	define('PR_SCHDINFO_FREEBUSY_OOF', 1750470914);
}
if (!defined('PR_FREEBUSY_RANGE_TIMESTAMP')) {
	define('PR_FREEBUSY_RANGE_TIMESTAMP', 1751646272);
}
if (!defined('PR_FREEBUSY_COUNT_MONTHS')) {
	define('PR_FREEBUSY_COUNT_MONTHS', 1751711747);
}
if (!defined('PR_SCHDINFO_APPT_TOMBSTONE')) {
	define('PR_SCHDINFO_APPT_TOMBSTONE', 1751777538);
}
if (!defined('PR_DELEGATE_FLAGS')) {
	define('PR_DELEGATE_FLAGS', 1751846915);
}
if (!defined('PR_SCHDINFO_FREEBUSY')) {
	define('PR_SCHDINFO_FREEBUSY', 1751908610);
}
if (!defined('PR_SCHDINFO_AUTO_ACCEPT_APPTS')) {
	define('PR_SCHDINFO_AUTO_ACCEPT_APPTS', 1751973899);
}
if (!defined('PR_SCHDINFO_DISALLOW_RECURRING_APPTS')) {
	define('PR_SCHDINFO_DISALLOW_RECURRING_APPTS', 1752039435);
}
if (!defined('PR_SCHDINFO_DISALLOW_OVERLAPPING_APPTS')) {
	define('PR_SCHDINFO_DISALLOW_OVERLAPPING_APPTS', 1752104971);
}
if (!defined('PR_WLINK_CLIENTID')) {
	define('PR_WLINK_CLIENTID', 1754267906);
}
if (!defined('PR_WLINK_AB_EXSTOREEID')) {
	define('PR_WLINK_AB_EXSTOREEID', 1754333442);
}
if (!defined('PR_WLINK_RO_GROUP_TYPE')) {
	define('PR_WLINK_RO_GROUP_TYPE', 1754398723);
}
if (!defined('PR_SECURITY_FLAGS')) {
	define('PR_SECURITY_FLAGS', 1845559299);
}
if (!defined('PR_VD_BINARY')) {
	define('PR_VD_BINARY', 1879113986);
}
if (!defined('PR_VD_STRINGS')) {
	define('PR_VD_STRINGS', 1879179294);
}
if (!defined('PR_VD_NAME')) {
	define('PR_VD_NAME', 1879441438);
}
if (!defined('PR_VD_VERSION')) {
	define('PR_VD_VERSION', 1879506947);
}
if (!defined('PR_FAV_DISPLAY_NAME')) {
	define('PR_FAV_DISPLAY_NAME', 2080374814);
}
if (!defined('PR_FAV_DISPLAY_ALIAS')) {
	define('PR_FAV_DISPLAY_ALIAS', 2080440350);
}
if (!defined('PR_FAV_PUBLIC_SOURCE_KEY')) {
	define('PR_FAV_PUBLIC_SOURCE_KEY', 2080506114);
}
if (!defined('PR_OST_OSTID')) {
	define('PR_OST_OSTID', 2080637186);
}
if (!defined('PR_OFFLINE_FOLDER')) {
	define('PR_OFFLINE_FOLDER', 2080702722);
}
if (!defined('PR_ROAMING_DATATYPES')) {
	define('PR_ROAMING_DATATYPES', 2080768003);
}
if (!defined('PR_ROAMING_DICTIONARY')) {
	define('PR_ROAMING_DICTIONARY', 2080833794);
}
if (!defined('PR_ROAMING_BINARYSTREAM')) {
	define('PR_ROAMING_BINARYSTREAM', 2080964866);
}
if (!defined('PR_STORE_SLOWLINK')) {
	define('PR_STORE_SLOWLINK', 2081030155);
}
if (!defined('PR_OSC_SYNC_ENABLEDONSERVER')) {
	define('PR_OSC_SYNC_ENABLEDONSERVER', 2082734091);
}
if (!defined('PR_FORCE_USE_ENTRYID_SERVER')) {
	define('PR_FORCE_USE_ENTRYID_SERVER', 2097020939);
}
if (!defined('PR_PROFILE_MDB_DN')) {
	define('PR_PROFILE_MDB_DN', 2097086494);
}
if (!defined('PR_FAV_AUTOSUBFOLDERS')) {
	define('PR_FAV_AUTOSUBFOLDERS', 2097217539);
}
if (!defined('PR_PROCESSED')) {
	define('PR_PROCESSED', 2097217547);
}
if (!defined('PR_FAV_PARENT_SOURCE_KEY')) {
	define('PR_FAV_PARENT_SOURCE_KEY', 2097283330);
}
if (!defined('PR_FAV_LEVEL_MASK')) {
	define('PR_FAV_LEVEL_MASK', 2097348611);
}
if (!defined('PR_FAV_KNOWN_SUBS')) {
	define('PR_FAV_KNOWN_SUBS', 2097414402);
}
if (!defined('PR_FAV_GUID_MAP')) {
	define('PR_FAV_GUID_MAP', 2097479938);
}
if (!defined('PR_FAV_KNOWN_DELS_OLD')) {
	define('PR_FAV_KNOWN_DELS_OLD', 2097545474);
}
if (!defined('PR_FAV_INHERIT_AUTO')) {
	define('PR_FAV_INHERIT_AUTO', 2097610755);
}
if (!defined('PR_FAV_DEL_SUBS')) {
	define('PR_FAV_DEL_SUBS', 2097676546);
}
if (!defined('PR_FAV_CONTAINER_CLASS')) {
	define('PR_FAV_CONTAINER_CLASS', 2097741854);
}
if (!defined('PR_EXCEPTION_REPLACETIME')) {
	define('PR_EXCEPTION_REPLACETIME', 2147024960);
}
if (!defined('PR_ATTACHMENT_LINKID')) {
	define('PR_ATTACHMENT_LINKID', 2147090435);
}
if (!defined('PR_EXCEPTION_STARTTIME')) {
	define('PR_EXCEPTION_STARTTIME', 2147156032);
}
if (!defined('PR_EXCEPTION_ENDTIME')) {
	define('PR_EXCEPTION_ENDTIME', 2147221568);
}
if (!defined('PR_ATTACHMENT_FLAGS')) {
	define('PR_ATTACHMENT_FLAGS', 2147287043);
}
if (!defined('PR_ATTACHMENT_HIDDEN')) {
	define('PR_ATTACHMENT_HIDDEN', 2147352587);
}
if (!defined('PR_ATTACHMENT_CONTACTPHOTO')) {
	define('PR_ATTACHMENT_CONTACTPHOTO', 2147418123);
}
if (!defined('PR_EMS_AB_FOLDER_PATHNAME')) {
	define('PR_EMS_AB_FOLDER_PATHNAME', 2147745822);
}
if (!defined('PR_EMS_AB_MANAGER')) {
	define('PR_EMS_AB_MANAGER', 2147811341);
}
if (!defined('PR_EMS_AB_MANAGER_T')) {
	define('PR_EMS_AB_MANAGER_T', 2147811358);
}
if (!defined('PR_EMS_AB_HOME_MDB')) {
	define('PR_EMS_AB_HOME_MDB', 2147876894);
}
if (!defined('PR_EMS_AB_HOME_MDB_A')) {
	define('PR_EMS_AB_HOME_MDB_A', 2147876894);
}
if (!defined('PR_EMS_AB_IS_MEMBER_OF_DL')) {
	define('PR_EMS_AB_IS_MEMBER_OF_DL', 2148007966);
}
if (!defined('PR_EMS_AB_MEMBER')) {
	define('PR_EMS_AB_MEMBER', 2148073485);
}
if (!defined('PR_EMS_AB_OWNER')) {
	define('PR_EMS_AB_OWNER', 2148270338);
}
if (!defined('PR_EMS_AB_OWNER_O')) {
	define('PR_EMS_AB_OWNER_O', 2148270093);
}
if (!defined('PR_EMS_AB_REPORTS')) {
	define('PR_EMS_AB_REPORTS', 2148401165);
}
if (!defined('PR_EMS_AB_REPORTS_MV')) {
	define('PR_EMS_AB_REPORTS_MV', 2148405506);
}
if (!defined('PR_EMS_AB_PROXY_ADDRESSES')) {
	define('PR_EMS_AB_PROXY_ADDRESSES', 2148470814);
}
if (!defined('PR_EMS_AB_PROXY_ADDRESSES_A')) {
	define('PR_EMS_AB_PROXY_ADDRESSES_A', 2148470814);
}
if (!defined('PR_EMS_AB_PROXY_ADDRESSES_MV')) {
	define('PR_EMS_AB_PROXY_ADDRESSES_MV', 2148470814);
}
if (!defined('PR_EMS_AB_TARGET_ADDRESS')) {
	define('PR_EMS_AB_TARGET_ADDRESS', 2148597790);
}
if (!defined('PR_EMS_AB_PUBLIC_DELEGATES')) {
	define('PR_EMS_AB_PUBLIC_DELEGATES', 2148859917);
}
if (!defined('PR_EMS_AB_OWNER_BL_O')) {
	define('PR_EMS_AB_OWNER_BL_O', 2149842957);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_1')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_1', 2150432798);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_2')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_2', 2150498334);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_3')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_3', 2150563870);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_4')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_4', 2150629406);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_5')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_5', 2150694942);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_6')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_6', 2150760478);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_7')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_7', 2150826014);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_8')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_8', 2150891550);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_9')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_9', 2150957086);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_10')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_10', 2151022622);
}
if (!defined('PR_EMS_AB_OBJ_DIST_NAME')) {
	define('PR_EMS_AB_OBJ_DIST_NAME', 2151415838);
}
if (!defined('PR_EMS_AB_DELIV_CONT_LENGTH')) {
	define('PR_EMS_AB_DELIV_CONT_LENGTH', 2154430467);
}
if (!defined('PR_EMS_AB_DL_MEM_SUBMIT_PERMS_BL_O')) {
	define('PR_EMS_AB_DL_MEM_SUBMIT_PERMS_BL_O', 2155020301);
}
if (!defined('PR_EMS_AB_NETWORK_ADDRESS')) {
	define('PR_EMS_AB_NETWORK_ADDRESS', 2171605022);
}
if (!defined('PR_EMS_AB_NETWORK_ADDRESS_A')) {
	define('PR_EMS_AB_NETWORK_ADDRESS_A', 2171605022);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_11')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_11', 2354511902);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_12')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_12', 2354577438);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_13')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_13', 2354642974);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_14')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_14', 2355101726);
}
if (!defined('PR_EMS_AB_EXTENSION_ATTRIBUTE_15')) {
	define('PR_EMS_AB_EXTENSION_ATTRIBUTE_15', 2355167262);
}
if (!defined('PR_EMS_AB_X509_CERT')) {
	define('PR_EMS_AB_X509_CERT', 2355761410);
}
if (!defined('PR_EMS_AB_OBJECT_GUID')) {
	define('PR_EMS_AB_OBJECT_GUID', 2355953922);
}
if (!defined('PR_EMS_AB_PHONETIC_GIVEN_NAME')) {
	define('PR_EMS_AB_PHONETIC_GIVEN_NAME', 2358116382);
}
if (!defined('PR_EMS_AB_PHONETIC_SURNAME')) {
	define('PR_EMS_AB_PHONETIC_SURNAME', 2358181918);
}
if (!defined('PR_EMS_AB_PHONETIC_DEPARTMENT_NAME')) {
	define('PR_EMS_AB_PHONETIC_DEPARTMENT_NAME', 2358247454);
}
if (!defined('PR_EMS_AB_PHONETIC_COMPANY_NAME')) {
	define('PR_EMS_AB_PHONETIC_COMPANY_NAME', 2358312990);
}
if (!defined('PR_EMS_AB_PHONETIC_DISPLAY_NAME')) {
	define('PR_EMS_AB_PHONETIC_DISPLAY_NAME', 2358378526);
}
if (!defined('PR_EMS_AB_PHONETIC_DISPLAY_NAME_A')) {
	define('PR_EMS_AB_PHONETIC_DISPLAY_NAME_A', 2358378526);
}
if (!defined('PR_EMS_AB_DISPLAY_TYPE_EX')) {
	define('PR_EMS_AB_DISPLAY_TYPE_EX', 2358444035);
}
if (!defined('PR_EMS_AB_HAB_SHOW_IN_DEPARTMENTS')) {
	define('PR_EMS_AB_HAB_SHOW_IN_DEPARTMENTS', 2358509581);
}
if (!defined('PR_EMS_AB_ROOM_CONTAINERS')) {
	define('PR_EMS_AB_ROOM_CONTAINERS', 2358644766);
}
if (!defined('PR_EMS_AB_HAB_DEPARTMENT_MEMBERS')) {
	define('PR_EMS_AB_HAB_DEPARTMENT_MEMBERS', 2358706189);
}
if (!defined('PR_EMS_AB_HAB_ROOT_DEPARTMENT')) {
	define('PR_EMS_AB_HAB_ROOT_DEPARTMENT', 2358771742);
}
if (!defined('PR_EMS_AB_HAB_PARENT_DEPARTMENT')) {
	define('PR_EMS_AB_HAB_PARENT_DEPARTMENT', 2358837261);
}
if (!defined('PR_EMS_AB_HAB_CHILD_DEPARTMENTS')) {
	define('PR_EMS_AB_HAB_CHILD_DEPARTMENTS', 2358902797);
}
if (!defined('PR_EMS_AB_THUMBNAIL_PHOTO')) {
	define('PR_EMS_AB_THUMBNAIL_PHOTO', 2359165186);
}
if (!defined('PR_EMS_AB_HAB_SENIORITY_INDEX')) {
	define('PR_EMS_AB_HAB_SENIORITY_INDEX', 2359296003);
}
if (!defined('PR_EMS_AB_ORG_UNIT_ROOT_DN')) {
	define('PR_EMS_AB_ORG_UNIT_ROOT_DN', 2359820318);
}
if (!defined('PR_EMS_AB_DL_SENDER_HINT_TRANSLATIONS')) {
	define('PR_EMS_AB_DL_SENDER_HINT_TRANSLATIONS', 2360086558);
}
if (!defined('PR_EMS_AB_ENABLE_MODERATION')) {
	define('PR_EMS_AB_ENABLE_MODERATION', 2360672267);
}
if (!defined('PR_EMS_AB_UM_SPOKEN_NAME')) {
	define('PR_EMS_AB_UM_SPOKEN_NAME', 2361524482);
}
if (!defined('PR_EMS_AB_AUTH_ORIG')) {
	define('PR_EMS_AB_AUTH_ORIG', 2362966029);
}
if (!defined('PR_EMS_AB_UNAUTH_ORIG')) {
	define('PR_EMS_AB_UNAUTH_ORIG', 2363031565);
}
if (!defined('PR_EMS_AB_DL_MEM_SUBMIT_PERMS')) {
	define('PR_EMS_AB_DL_MEM_SUBMIT_PERMS', 2363097101);
}
if (!defined('PR_EMS_AB_DL_MEM_REJECT_PERMS')) {
	define('PR_EMS_AB_DL_MEM_REJECT_PERMS', 2363162637);
}
if (!defined('PR_EMS_AB_HAB_IS_HIERARCHICAL_GROUP')) {
	define('PR_EMS_AB_HAB_IS_HIERARCHICAL_GROUP', 2363293707);
}
if (!defined('PR_EMS_AB_DL_TOTAL_MEMBER_COUNT')) {
	define('PR_EMS_AB_DL_TOTAL_MEMBER_COUNT', 2363621379);
}
if (!defined('PR_EMS_AB_DL_EXTERNAL_MEMBER_COUNT')) {
	define('PR_EMS_AB_DL_EXTERNAL_MEMBER_COUNT', 2363686915);
}
if (!defined('PR_EMS_AB_IS_MASTER')) {
	define('PR_EMS_AB_IS_MASTER', 4294639627);
}
if (!defined('PR_EMS_AB_PARENT_ENTRYID')) {
	define('PR_EMS_AB_PARENT_ENTRYID', 4294705410);
}
if (!defined('PR_EMS_AB_CONTAINERID')) {
	define('PR_EMS_AB_CONTAINERID', 4294770691);
}
if (!defined('PidLidAttendeeCriticalChange')) {
	define('PidLidAttendeeCriticalChange', 1);
}
if (!defined('PidLidGlobalObjectId')) {
	define('PidLidGlobalObjectId', 3);
}
if (!defined('PidLidIsException')) {
	define('PidLidIsException', 10);
}
if (!defined('PidLidStartRecurrenceTime')) {
	define('PidLidStartRecurrenceTime', 14);
}
if (!defined('PidLidOwnerCriticalChange')) {
	define('PidLidOwnerCriticalChange', 26);
}
if (!defined('PidLidCleanGlobalObjectId')) {
	define('PidLidCleanGlobalObjectId', 35);
}
if (!defined('PidLidCategories')) {
	define('PidLidCategories', 9000);
}
if (!defined('PidLidFileAs')) {
	define('PidLidFileAs', 32773);
}
if (!defined('PidLidHomeAddress')) {
	define('PidLidHomeAddress', 32794);
}
if (!defined('PidLidBusinessAddress')) {
	define('PidLidBusinessAddress', 32795);
}
if (!defined('PidLidOtherAddress')) {
	define('PidLidOtherAddress', 32796);
}
if (!defined('PidLidMailingAdress')) {
	define('PidLidMailingAdress', 32802);
}
if (!defined('PidLidPostalAddressIndex')) {
	define('PidLidPostalAddressIndex', 32802);
}
if (!defined('PidLidBusinessCardDisplayDefinition')) {
	define('PidLidBusinessCardDisplayDefinition', 32832);
}
if (!defined('PidLidWorkAddressStreet')) {
	define('PidLidWorkAddressStreet', 32837);
}
if (!defined('PidLidWorkAddressCity')) {
	define('PidLidWorkAddressCity', 32838);
}
if (!defined('PidLidWorkAddressState')) {
	define('PidLidWorkAddressState', 32839);
}
if (!defined('PidLidWorkAddressPostalCode')) {
	define('PidLidWorkAddressPostalCode', 32840);
}
if (!defined('PidLidWorkAddressCountry')) {
	define('PidLidWorkAddressCountry', 32841);
}
if (!defined('PidLidWorkAddressPostOfficeBox')) {
	define('PidLidWorkAddressPostOfficeBox', 32842);
}
if (!defined('PidLidContactUserField1')) {
	define('PidLidContactUserField1', 32847);
}
if (!defined('PidLidContactUserField2')) {
	define('PidLidContactUserField2', 32848);
}
if (!defined('PidLidContactUserField3')) {
	define('PidLidContactUserField3', 32849);
}
if (!defined('PidLidContactUserField4')) {
	define('PidLidContactUserField4', 32850);
}
if (!defined('PidLidInstantMessagingAddress')) {
	define('PidLidInstantMessagingAddress', 32866);
}
if (!defined('PidLidEmail1DisplayName')) {
	define('PidLidEmail1DisplayName', 32896);
}
if (!defined('PidLidEmail1AddressType')) {
	define('PidLidEmail1AddressType', 32898);
}
if (!defined('PidLidEmail1EmailAddress')) {
	define('PidLidEmail1EmailAddress', 32899);
}
if (!defined('PidLidEmail2DisplayName')) {
	define('PidLidEmail2DisplayName', 32912);
}
if (!defined('PidLidEmail2AddressType')) {
	define('PidLidEmail2AddressType', 32914);
}
if (!defined('PidLidEmail2EmailAddress')) {
	define('PidLidEmail2EmailAddress', 32915);
}
if (!defined('PidLidEmail3DisplayName')) {
	define('PidLidEmail3DisplayName', 32928);
}
if (!defined('PidLidEmail3AddressType')) {
	define('PidLidEmail3AddressType', 32930);
}
if (!defined('PidLidEmail3EmailAddress')) {
	define('PidLidEmail3EmailAddress', 32931);
}
if (!defined('PidLidFreeBusyLocation')) {
	define('PidLidFreeBusyLocation', 32984);
}
if (!defined('PidLidTaskStatus')) {
	define('PidLidTaskStatus', 33025);
}
if (!defined('PidLidPercentComplete')) {
	define('PidLidPercentComplete', 33026);
}
if (!defined('PidLidTaskStartDate')) {
	define('PidLidTaskStartDate', 33028);
}
if (!defined('PidLidTaskDueDate')) {
	define('PidLidTaskDueDate', 33029);
}
if (!defined('PidLidTaskDateCompleted')) {
	define('PidLidTaskDateCompleted', 33039);
}
if (!defined('PidLidTaskActualEffort')) {
	define('PidLidTaskActualEffort', 33040);
}
if (!defined('PidLidTaskEstimatedEffort')) {
	define('PidLidTaskEstimatedEffort', 33041);
}
if (!defined('PidLidTaskRecurrence')) {
	define('PidLidTaskRecurrence', 33046);
}
if (!defined('PidLidTaskComplete')) {
	define('PidLidTaskComplete', 33052);
}
if (!defined('PidLidTaskOwner')) {
	define('PidLidTaskOwner', 33055);
}
if (!defined('PidLidTaskFRecurring')) {
	define('PidLidTaskFRecurring', 33062);
}
if (!defined('PidLidAppointmentSequence')) {
	define('PidLidAppointmentSequence', 33281);
}
if (!defined('PidLidBusyStatus')) {
	define('PidLidBusyStatus', 33285);
}
if (!defined('PidLidLocation')) {
	define('PidLidLocation', 33288);
}
if (!defined('PidLidAppointmentReplyTime')) {
	define('PidLidAppointmentReplyTime', 33312);
}
if (!defined('PidLidAppointmentStartWhole')) {
	define('PidLidAppointmentStartWhole', 33293);
}
if (!defined('PidLidAppointmentEndWhole')) {
	define('PidLidAppointmentEndWhole', 33294);
}
if (!defined('PidLidAppointmentDuration')) {
	define('PidLidAppointmentDuration', 33299);
}
if (!defined('PidLidAppointmentSubType')) {
	define('PidLidAppointmentSubType', 33301);
}
if (!defined('PidLidAppointmentRecur')) {
	define('PidLidAppointmentRecur', 33302);
}
if (!defined('PidLidAppointmentStateFlags')) {
	define('PidLidAppointmentStateFlags', 33303);
}
if (!defined('PidLidResponseStatus')) {
	define('PidLidResponseStatus', 33304);
}
if (!defined('PidLidRecurring')) {
	define('PidLidRecurring', 33315);
}
if (!defined('PidLidIntendedBusyStatus')) {
	define('PidLidIntendedBusyStatus', 33316);
}
if (!defined('PidLidExceptionReplaceTime')) {
	define('PidLidExceptionReplaceTime', 33320);
}
if (!defined('PidLidFInvited')) {
	define('PidLidFInvited', 33321);
}
if (!defined('PidLidRecurrenceType')) {
	define('PidLidRecurrenceType', 33329);
}
if (!defined('PidLidRecurrencePattern')) {
	define('PidLidRecurrencePattern', 33330);
}
if (!defined('PidLidTimeZoneStruct')) {
	define('PidLidTimeZoneStruct', 33331);
}
if (!defined('PidLidTimeZoneDescription')) {
	define('PidLidTimeZoneDescription', 33332);
}
if (!defined('PidLidClipStart')) {
	define('PidLidClipStart', 33333);
}
if (!defined('PidLidClipEnd')) {
	define('PidLidClipEnd', 33334);
}
if (!defined('PidLidAppointmentProposedStartWhole')) {
	define('PidLidAppointmentProposedStartWhole', 33360);
}
if (!defined('PidLidAppointmentProposedEndWhole')) {
	define('PidLidAppointmentProposedEndWhole', 33361);
}
if (!defined('PidLidAppointmentCounterProposal')) {
	define('PidLidAppointmentCounterProposal', 33367);
}
if (!defined('PidLidAppointmentNotAllowPropose')) {
	define('PidLidAppointmentNotAllowPropose', 33370);
}
if (!defined('PidLidAppointmentTimeZoneDefinitionStartDisplay')) {
	define('PidLidAppointmentTimeZoneDefinitionStartDisplay', 33374);
}
if (!defined('PidLidAppointmentTimeZoneDefinitionEndDisplay')) {
	define('PidLidAppointmentTimeZoneDefinitionEndDisplay', 33375);
}
if (!defined('PidLidAppointmentTimeZoneDefinitionRecur')) {
	define('PidLidAppointmentTimeZoneDefinitionRecur', 33376);
}
if (!defined('PidLidReminderDelta')) {
	define('PidLidReminderDelta', 34049);
}
if (!defined('PidLidReminderTime')) {
	define('PidLidReminderTime', 34050);
}
if (!defined('PidLidReminderSet')) {
	define('PidLidReminderSet', 34051);
}
if (!defined('PidLidPrivate')) {
	define('PidLidPrivate', 34054);
}
if (!defined('PidLidSmartNoAttach')) {
	define('PidLidSmartNoAttach', 34068);
}
if (!defined('PidLidCommonStart')) {
	define('PidLidCommonStart', 34070);
}
if (!defined('PidLidCommonEnd')) {
	define('PidLidCommonEnd', 34071);
}
if (!defined('PidLidFlagRequest')) {
	define('PidLidFlagRequest', 34096);
}
if (!defined('PidLidMileage')) {
	define('PidLidMileage', 34100);
}
if (!defined('PidLidBilling')) {
	define('PidLidBilling', 34101);
}
if (!defined('PidLidCompanies')) {
	define('PidLidCompanies', 34105);
}
if (!defined('PidLidReminderSignalTime')) {
	define('PidLidReminderSignalTime', 34144);
}
if (!defined('PidLidToDoTitle')) {
	define('PidLidToDoTitle', 34212);
}
if (!defined('PidLidInfoPathFromName')) {
	define('PidLidInfoPathFromName', 34225);
}
if (!defined('PidLidClassified')) {
	define('PidLidClassified', 34229);
}
if (!defined('PidLidClassification')) {
	define('PidLidClassification', 34230);
}
if (!defined('PidLidClassificationDescription')) {
	define('PidLidClassificationDescription', 34231);
}
if (!defined('PidLidClassificationGuid')) {
	define('PidLidClassificationGuid', 34232);
}
if (!defined('PidLidClassificationKeep')) {
	define('PidLidClassificationKeep', 34234);
}
if (!defined('ecSuccess')) {
	define('ecSuccess', 0);
}
if (!defined('MAPI_E_USER_ABORT')) {
	define('MAPI_E_USER_ABORT', 1);
}
if (!defined('MAPI_E_FAILURE')) {
	define('MAPI_E_FAILURE', 2);
}
if (!defined('MAPI_E_LOGON_FAILURE')) {
	define('MAPI_E_LOGON_FAILURE', 3);
}
if (!defined('MAPI_E_DISK_FULL')) {
	define('MAPI_E_DISK_FULL', 4);
}
if (!defined('MAPI_E_INSUFFICIENT_MEMORY')) {
	define('MAPI_E_INSUFFICIENT_MEMORY', 5);
}
if (!defined('MAPI_E_ACCESS_DENIED')) {
	define('MAPI_E_ACCESS_DENIED', 6);
}
if (!defined('MAPI_E_TOO_MANY_SESSIONS')) {
	define('MAPI_E_TOO_MANY_SESSIONS', 8);
}
if (!defined('MAPI_E_TOO_MANY_FILES')) {
	define('MAPI_E_TOO_MANY_FILES', 9);
}
if (!defined('MAPI_E_TOO_MANY_RECIPIENTS')) {
	define('MAPI_E_TOO_MANY_RECIPIENTS', 10);
}
if (!defined('MAPI_E_ATTACHMENT_NOT_FOUND')) {
	define('MAPI_E_ATTACHMENT_NOT_FOUND', 11);
}
if (!defined('MAPI_E_ATTACHMENT_OPEN_FAILURE')) {
	define('MAPI_E_ATTACHMENT_OPEN_FAILURE', 12);
}
if (!defined('MAPI_E_ATTACHMENT_WRITE_FAILURE')) {
	define('MAPI_E_ATTACHMENT_WRITE_FAILURE', 13);
}
if (!defined('MAPI_E_UNKNOWN_RECIPIENT')) {
	define('MAPI_E_UNKNOWN_RECIPIENT', 14);
}
if (!defined('MAPI_E_BAD_RECIPTYPE')) {
	define('MAPI_E_BAD_RECIPTYPE', 15);
}
if (!defined('MAPI_E_NO_MESSAGES')) {
	define('MAPI_E_NO_MESSAGES', 16);
}
if (!defined('MAPI_E_INVALID_MESSAGE')) {
	define('MAPI_E_INVALID_MESSAGE', 17);
}
if (!defined('MAPI_E_TEXT_TOO_LARGE')) {
	define('MAPI_E_TEXT_TOO_LARGE', 18);
}
if (!defined('MAPI_E_INVALID_SESSION')) {
	define('MAPI_E_INVALID_SESSION', 19);
}
if (!defined('MAPI_E_TYPE_NOT_SUPPORTED')) {
	define('MAPI_E_TYPE_NOT_SUPPORTED', 20);
}
if (!defined('MAPI_E_AMBIGUOUS_RECIPIENT')) {
	define('MAPI_E_AMBIGUOUS_RECIPIENT', 21);
}
if (!defined('MAPI_E_MESSAGE_IN_USE')) {
	define('MAPI_E_MESSAGE_IN_USE', 22);
}
if (!defined('MAPI_E_NETWORK_FAILURE')) {
	define('MAPI_E_NETWORK_FAILURE', 23);
}
if (!defined('MAPI_E_INVALID_EDITFIELDS')) {
	define('MAPI_E_INVALID_EDITFIELDS', 24);
}
if (!defined('MAPI_E_INVALID_RECIPS')) {
	define('MAPI_E_INVALID_RECIPS', 25);
}
if (!defined('MAPI_E_NOT_SUPPORTED')) {
	define('MAPI_E_NOT_SUPPORTED', 26);
}
if (!defined('ecJetError')) {
	define('ecJetError', 1002);
}
if (!defined('ecUnknownUser')) {
	define('ecUnknownUser', 1003);
}
if (!defined('ecExiting')) {
	define('ecExiting', 1005);
}
if (!defined('ecBadConfig')) {
	define('ecBadConfig', 1006);
}
if (!defined('ecUnknownCodePage')) {
	define('ecUnknownCodePage', 1007);
}
if (!defined('ecServerOOM')) {
	define('ecServerOOM', 1008);
}
if (!defined('ecLoginPerm')) {
	define('ecLoginPerm', 1010);
}
if (!defined('ecDatabaseRolledBack')) {
	define('ecDatabaseRolledBack', 1011);
}
if (!defined('ecDatabaseCopiedError')) {
	define('ecDatabaseCopiedError', 1012);
}
if (!defined('ecAuditNotAllowed')) {
	define('ecAuditNotAllowed', 1013);
}
if (!defined('ecZombieUser')) {
	define('ecZombieUser', 1014);
}
if (!defined('ecUnconvertableACL')) {
	define('ecUnconvertableACL', 1015);
}
if (!defined('ecNoFreeJses')) {
	define('ecNoFreeJses', 1100);
}
if (!defined('ecDifferentJses')) {
	define('ecDifferentJses', 1101);
}
if (!defined('ecFileRemove')) {
	define('ecFileRemove', 1103);
}
if (!defined('ecParameterOverflow')) {
	define('ecParameterOverflow', 1104);
}
if (!defined('ecBadVersion')) {
	define('ecBadVersion', 1105);
}
if (!defined('ecTooManyCols')) {
	define('ecTooManyCols', 1106);
}
if (!defined('ecHaveMore')) {
	define('ecHaveMore', 1107);
}
if (!defined('ecDatabaseError')) {
	define('ecDatabaseError', 1108);
}
if (!defined('ecIndexNameTooBig')) {
	define('ecIndexNameTooBig', 1109);
}
if (!defined('ecUnsupportedProp')) {
	define('ecUnsupportedProp', 1110);
}
if (!defined('ecMsgNotSaved')) {
	define('ecMsgNotSaved', 1111);
}
if (!defined('ecUnpubNotif')) {
	define('ecUnpubNotif', 1113);
}
if (!defined('ecDifferentRoot')) {
	define('ecDifferentRoot', 1115);
}
if (!defined('ecBadFolderName')) {
	define('ecBadFolderName', 1116);
}
if (!defined('ecAttachOpen')) {
	define('ecAttachOpen', 1117);
}
if (!defined('ecInvClpsState')) {
	define('ecInvClpsState', 1118);
}
if (!defined('ecSkipMyChildren')) {
	define('ecSkipMyChildren', 1119);
}
if (!defined('ecSearchFolder')) {
	define('ecSearchFolder', 1120);
}
if (!defined('ecNotSearchFolder')) {
	define('ecNotSearchFolder', 1121);
}
if (!defined('ecFolderSetReceive')) {
	define('ecFolderSetReceive', 1122);
}
if (!defined('ecNoReceiveFolder')) {
	define('ecNoReceiveFolder', 1123);
}
if (!defined('ecNoDelSubmitMsg')) {
	define('ecNoDelSubmitMsg', 1125);
}
if (!defined('ecInvalidRecips')) {
	define('ecInvalidRecips', 1127);
}
if (!defined('ecNoReplicaHere')) {
	define('ecNoReplicaHere', 1128);
}
if (!defined('ecNoReplicaAvailable')) {
	define('ecNoReplicaAvailable', 1129);
}
if (!defined('ecPublicMDB')) {
	define('ecPublicMDB', 1130);
}
if (!defined('ecNotPublicMDB')) {
	define('ecNotPublicMDB', 1131);
}
if (!defined('ecRecordNotFound')) {
	define('ecRecordNotFound', 1132);
}
if (!defined('ecReplConflict')) {
	define('ecReplConflict', 1133);
}
if (!defined('ecFxBufferOverrun')) {
	define('ecFxBufferOverrun', 1136);
}
if (!defined('ecFxBufferEmpty')) {
	define('ecFxBufferEmpty', 1137);
}
if (!defined('ecFxPartialValue')) {
	define('ecFxPartialValue', 1138);
}
if (!defined('ecFxNoRoom')) {
	define('ecFxNoRoom', 1139);
}
if (!defined('ecMaxTimeExpired')) {
	define('ecMaxTimeExpired', 1140);
}
if (!defined('ecDstError')) {
	define('ecDstError', 1141);
}
if (!defined('ecMDBNotInit')) {
	define('ecMDBNotInit', 1142);
}
if (!defined('ecWrongServer')) {
	define('ecWrongServer', 1144);
}
if (!defined('ecBufferTooSmall')) {
	define('ecBufferTooSmall', 1149);
}
if (!defined('ecRequiresRefResolve')) {
	define('ecRequiresRefResolve', 1150);
}
if (!defined('ecServerPaused')) {
	define('ecServerPaused', 1151);
}
if (!defined('ecServerBusy')) {
	define('ecServerBusy', 1152);
}
if (!defined('ecNoSuchLogon')) {
	define('ecNoSuchLogon', 1153);
}
if (!defined('ecLoadLibFailed')) {
	define('ecLoadLibFailed', 1154);
}
if (!defined('ecObjAlreadyConfig')) {
	define('ecObjAlreadyConfig', 1155);
}
if (!defined('ecObjNotConfig')) {
	define('ecObjNotConfig', 1156);
}
if (!defined('ecDataLoss')) {
	define('ecDataLoss', 1157);
}
if (!defined('ecMaxSendThreadExceeded')) {
	define('ecMaxSendThreadExceeded', 1160);
}
if (!defined('ecFxErrorMarker')) {
	define('ecFxErrorMarker', 1161);
}
if (!defined('ecNoFreeJtabs')) {
	define('ecNoFreeJtabs', 1162);
}
if (!defined('ecNotPrivateMDB')) {
	define('ecNotPrivateMDB', 1163);
}
if (!defined('ecIsintegMDB')) {
	define('ecIsintegMDB', 1164);
}
if (!defined('ecRecoveryMDBMismatch')) {
	define('ecRecoveryMDBMismatch', 1165);
}
if (!defined('ecTableMayNotBeDeleted')) {
	define('ecTableMayNotBeDeleted', 1166);
}
if (!defined('ecSearchFolderScopeViolation')) {
	define('ecSearchFolderScopeViolation', 1168);
}
if (!defined('ecRpcRegisterIf')) {
	define('ecRpcRegisterIf', 1201);
}
if (!defined('ecRpcListen')) {
	define('ecRpcListen', 1202);
}
if (!defined('ecRpcFormat')) {
	define('ecRpcFormat', 1206);
}
if (!defined('ecNoCopyTo')) {
	define('ecNoCopyTo', 1207);
}
if (!defined('ecNullObject')) {
	define('ecNullObject', 1209);
}
if (!defined('ecRpcAuthentication')) {
	define('ecRpcAuthentication', 1212);
}
if (!defined('ecRpcBadAuthenticationLevel')) {
	define('ecRpcBadAuthenticationLevel', 1213);
}
if (!defined('ecNullCommentRestriction')) {
	define('ecNullCommentRestriction', 1214);
}
if (!defined('ecRulesLoadError')) {
	define('ecRulesLoadError', 1228);
}
if (!defined('ecRulesDelivErr')) {
	define('ecRulesDelivErr', 1229);
}
if (!defined('ecRulesParsingErr')) {
	define('ecRulesParsingErr', 1230);
}
if (!defined('ecRulesCreateDaeErr')) {
	define('ecRulesCreateDaeErr', 1231);
}
if (!defined('ecRulesCreateDamErr')) {
	define('ecRulesCreateDamErr', 1232);
}
if (!defined('ecRulesNoMoveCopyFolder')) {
	define('ecRulesNoMoveCopyFolder', 1233);
}
if (!defined('ecRulesNoFolderRights')) {
	define('ecRulesNoFolderRights', 1234);
}
if (!defined('ecMessageTooBig')) {
	define('ecMessageTooBig', 1236);
}
if (!defined('ecFormNotValid')) {
	define('ecFormNotValid', 1237);
}
if (!defined('ecNotAuthorized')) {
	define('ecNotAuthorized', 1238);
}
if (!defined('ecDeleteMessage')) {
	define('ecDeleteMessage', 1239);
}
if (!defined('ecBounceMessage')) {
	define('ecBounceMessage', 1240);
}
if (!defined('ecQuotaExceeded')) {
	define('ecQuotaExceeded', 1241);
}
if (!defined('ecMaxSubmissionExceeded')) {
	define('ecMaxSubmissionExceeded', 1242);
}
if (!defined('ecMaxAttachmentExceeded')) {
	define('ecMaxAttachmentExceeded', 1243);
}
if (!defined('ecSendAsDenied')) {
	define('ecSendAsDenied', 1244);
}
if (!defined('ecShutoffQuotaExceeded')) {
	define('ecShutoffQuotaExceeded', 1245);
}
if (!defined('ecMaxObjsExceeded')) {
	define('ecMaxObjsExceeded', 1246);
}
if (!defined('ecClientVerDisallowed')) {
	define('ecClientVerDisallowed', 1247);
}
if (!defined('ecRpcHttpDisallowed')) {
	define('ecRpcHttpDisallowed', 1248);
}
if (!defined('ecCachedModeRequired')) {
	define('ecCachedModeRequired', 1249);
}
if (!defined('ecFolderNotCleanedUp')) {
	define('ecFolderNotCleanedUp', 1251);
}
if (!defined('ecFmtError')) {
	define('ecFmtError', 1261);
}
if (!defined('ecNotExpanded')) {
	define('ecNotExpanded', 1271);
}
if (!defined('ecNotCollapsed')) {
	define('ecNotCollapsed', 1272);
}
if (!defined('ecLeaf')) {
	define('ecLeaf', 1273);
}
if (!defined('ecUnregisteredNamedProp')) {
	define('ecUnregisteredNamedProp', 1274);
}
if (!defined('ecFolderDisabled')) {
	define('ecFolderDisabled', 1275);
}
if (!defined('ecDomainError')) {
	define('ecDomainError', 1276);
}
if (!defined('ecNoCreateRight')) {
	define('ecNoCreateRight', 1279);
}
if (!defined('ecPublicRoot')) {
	define('ecPublicRoot', 1280);
}
if (!defined('ecNoReadRight')) {
	define('ecNoReadRight', 1281);
}
if (!defined('ecNoCreateSubfolderRight')) {
	define('ecNoCreateSubfolderRight', 1282);
}
if (!defined('ecDstNullObject')) {
	define('ecDstNullObject', 1283);
}
if (!defined('ecMsgCycle')) {
	define('ecMsgCycle', 1284);
}
if (!defined('ecTooManyRecips')) {
	define('ecTooManyRecips', 1285);
}
if (!defined('ecVirusScanInProgress')) {
	define('ecVirusScanInProgress', 1290);
}
if (!defined('ecVirusDetected')) {
	define('ecVirusDetected', 1291);
}
if (!defined('ecMailboxInTransit')) {
	define('ecMailboxInTransit', 1292);
}
if (!defined('ecBackupInProgress')) {
	define('ecBackupInProgress', 1293);
}
if (!defined('ecVirusMessageDeleted')) {
	define('ecVirusMessageDeleted', 1294);
}
if (!defined('ecInvalidBackupSequence')) {
	define('ecInvalidBackupSequence', 1295);
}
if (!defined('ecInvalidBackupType')) {
	define('ecInvalidBackupType', 1296);
}
if (!defined('ecTooManyBackupsInProgress')) {
	define('ecTooManyBackupsInProgress', 1297);
}
if (!defined('ecRestoreInProgress')) {
	define('ecRestoreInProgress', 1298);
}
if (!defined('ecDuplicateObject')) {
	define('ecDuplicateObject', 1401);
}
if (!defined('ecObjectNotFound')) {
	define('ecObjectNotFound', 1402);
}
if (!defined('ecFixupReplyRule')) {
	define('ecFixupReplyRule', 1403);
}
if (!defined('ecTemplateNotFound')) {
	define('ecTemplateNotFound', 1404);
}
if (!defined('ecRuleException')) {
	define('ecRuleException', 1405);
}
if (!defined('ecDSNoSuchObject')) {
	define('ecDSNoSuchObject', 1406);
}
if (!defined('ecMessageAlreadyTombstoned')) {
	define('ecMessageAlreadyTombstoned', 1407);
}
if (!defined('ecRequiresRWTransaction')) {
	define('ecRequiresRWTransaction', 1430);
}
if (!defined('ecPaused')) {
	define('ecPaused', 1550);
}
if (!defined('ecNotPaused')) {
	define('ecNotPaused', 1551);
}
if (!defined('ecWrongMailbox')) {
	define('ecWrongMailbox', 1608);
}
if (!defined('ecChgPassword')) {
	define('ecChgPassword', 1612);
}
if (!defined('ecPwdExpired')) {
	define('ecPwdExpired', 1613);
}
if (!defined('ecInvWkstn')) {
	define('ecInvWkstn', 1614);
}
if (!defined('ecInvLogonHrs')) {
	define('ecInvLogonHrs', 1615);
}
if (!defined('ecAcctDisabled')) {
	define('ecAcctDisabled', 1616);
}
if (!defined('ecRuleVersion')) {
	define('ecRuleVersion', 1700);
}
if (!defined('ecRuleFormat')) {
	define('ecRuleFormat', 1701);
}
if (!defined('ecRuleSendAsDenied')) {
	define('ecRuleSendAsDenied', 1702);
}
if (!defined('ecNoServerSupport')) {
	define('ecNoServerSupport', 1721);
}
if (!defined('ecLockTimedOut')) {
	define('ecLockTimedOut', 1722);
}
if (!defined('ecObjectLocked')) {
	define('ecObjectLocked', 1723);
}
if (!defined('ecInvalidLockNamespace')) {
	define('ecInvalidLockNamespace', 1725);
}
if (!defined('RPC_X_BAD_STUB_DATA')) {
	define('RPC_X_BAD_STUB_DATA', 1783);
}
if (!defined('ecMessageDeleted')) {
	define('ecMessageDeleted', 2006);
}
if (!defined('ecProtocolDisabled')) {
	define('ecProtocolDisabled', 2008);
}
if (!defined('ecClearTextLogonDisabled')) {
	define('ecClearTextLogonDisabled', 2009);
}
if (!defined('ecRejected')) {
	define('ecRejected', 2030);
}
if (!defined('ecAmbiguousAlias')) {
	define('ecAmbiguousAlias', 2202);
}
if (!defined('ecUnknownMailbox')) {
	define('ecUnknownMailbox', 2203);
}
if (!defined('ecExpReserved')) {
	define('ecExpReserved', 2300);
}
if (!defined('ecExpParseDepth')) {
	define('ecExpParseDepth', 2301);
}
if (!defined('ecExpFuncArgType')) {
	define('ecExpFuncArgType', 2302);
}
if (!defined('ecExpSyntax')) {
	define('ecExpSyntax', 2303);
}
if (!defined('ecExpBadStrToken')) {
	define('ecExpBadStrToken', 2304);
}
if (!defined('ecExpBadColToken')) {
	define('ecExpBadColToken', 2305);
}
if (!defined('ecExpTypeMismatch')) {
	define('ecExpTypeMismatch', 2306);
}
if (!defined('ecExpOpNotSupported')) {
	define('ecExpOpNotSupported', 2307);
}
if (!defined('ecExpDivByZero')) {
	define('ecExpDivByZero', 2308);
}
if (!defined('ecExpUnaryArgType')) {
	define('ecExpUnaryArgType', 2309);
}
if (!defined('ecNotLocked')) {
	define('ecNotLocked', 2400);
}
if (!defined('ecClientEvent')) {
	define('ecClientEvent', 2401);
}
if (!defined('ecCorruptEvent')) {
	define('ecCorruptEvent', 2405);
}
if (!defined('ecCorruptWatermark')) {
	define('ecCorruptWatermark', 2406);
}
if (!defined('ecEventError')) {
	define('ecEventError', 2407);
}
if (!defined('ecWatermarkError')) {
	define('ecWatermarkError', 2408);
}
if (!defined('ecNonCanonicalACL')) {
	define('ecNonCanonicalACL', 2409);
}
if (!defined('ecMailboxDisabled')) {
	define('ecMailboxDisabled', 2412);
}
if (!defined('ecRulesFolderOverQuota')) {
	define('ecRulesFolderOverQuota', 2413);
}
if (!defined('ecADUnavailable')) {
	define('ecADUnavailable', 2414);
}
if (!defined('ecADError')) {
	define('ecADError', 2415);
}
if (!defined('ecNotEncrypted')) {
	define('ecNotEncrypted', 2416);
}
if (!defined('ecADNotFound')) {
	define('ecADNotFound', 2417);
}
if (!defined('ecADPropertyError')) {
	define('ecADPropertyError', 2418);
}
if (!defined('ecRpcServerTooBusy')) {
	define('ecRpcServerTooBusy', 2419);
}
if (!defined('ecRpcOutOfMemory')) {
	define('ecRpcOutOfMemory', 2420);
}
if (!defined('ecRpcServerOutOfMemory')) {
	define('ecRpcServerOutOfMemory', 2421);
}
if (!defined('ecRpcOutOfResources')) {
	define('ecRpcOutOfResources', 2422);
}
if (!defined('ecRpcServerUnavailable')) {
	define('ecRpcServerUnavailable', 2423);
}
if (!defined('ecSecureSubmitError')) {
	define('ecSecureSubmitError', 2426);
}
if (!defined('ecEventsDeleted')) {
	define('ecEventsDeleted', 2428);
}
if (!defined('ecSubsystemStopping')) {
	define('ecSubsystemStopping', 2429);
}
if (!defined('ecSAUnavailable')) {
	define('ecSAUnavailable', 2430);
}
if (!defined('ecCIStopping')) {
	define('ecCIStopping', 2600);
}
if (!defined('ecFxInvalidState')) {
	define('ecFxInvalidState', 2601);
}
if (!defined('ecFxUnexpectedMarker')) {
	define('ecFxUnexpectedMarker', 2602);
}
if (!defined('ecDuplicateDelivery')) {
	define('ecDuplicateDelivery', 2603);
}
if (!defined('ecConditionViolation')) {
	define('ecConditionViolation', 2604);
}
if (!defined('ecMaxPoolExceeded')) {
	define('ecMaxPoolExceeded', 2605);
}
if (!defined('ecRpcInvalidHandle')) {
	define('ecRpcInvalidHandle', 2606);
}
if (!defined('ecEventNotFound')) {
	define('ecEventNotFound', 2607);
}
if (!defined('ecPropNotPromoted')) {
	define('ecPropNotPromoted', 2608);
}
if (!defined('ecLowMdbSpace')) {
	define('ecLowMdbSpace', 2609);
}
if (!defined('MAPI_W_NO_SERVICE')) {
	define('MAPI_W_NO_SERVICE', 262659);
}
if (!defined('MAPI_W_ERRORS_RETURNED')) {
	define('MAPI_W_ERRORS_RETURNED', 263040);
}
if (!defined('MAPI_W_POSITION_CHANGED')) {
	define('MAPI_W_POSITION_CHANGED', 263297);
}
if (!defined('MAPI_W_APPROX_COUNT')) {
	define('MAPI_W_APPROX_COUNT', 263298);
}
if (!defined('MAPI_W_CANCEL_MESSAGE')) {
	define('MAPI_W_CANCEL_MESSAGE', 263552);
}
if (!defined('MAPI_W_PARTIAL_COMPLETION')) {
	define('MAPI_W_PARTIAL_COMPLETION', 263808);
}
if (!defined('SYNC_W_PROGRESS')) {
	define('SYNC_W_PROGRESS', 264224);
}
if (!defined('SYNC_W_CLIENT_CHANGE_NEWER')) {
	define('SYNC_W_CLIENT_CHANGE_NEWER', 264225);
}
if (!defined('MAPI_E_INTERFACE_NOT_SUPPORTED')) {
	define('MAPI_E_INTERFACE_NOT_SUPPORTED', 2147500034);
}
if (!defined('MAPI_E_CALL_FAILED')) {
	define('MAPI_E_CALL_FAILED', 2147500037);
}
if (!defined('SYNC_E_ERROR')) {
	define('SYNC_E_ERROR', 2147500037);
}
if (!defined('MAPI_E_NO_SUPPORT')) {
	define('MAPI_E_NO_SUPPORT', 2147746050);
}
if (!defined('MAPI_E_BAD_CHARWIDTH')) {
	define('MAPI_E_BAD_CHARWIDTH', 2147746051);
}
if (!defined('MAPI_E_STRING_TOO_LONG')) {
	define('MAPI_E_STRING_TOO_LONG', 2147746053);
}
if (!defined('MAPI_E_UNKNOWN_FLAGS')) {
	define('MAPI_E_UNKNOWN_FLAGS', 2147746054);
}
if (!defined('SYNC_E_UNKNOWN_FLAGS')) {
	define('SYNC_E_UNKNOWN_FLAGS', 2147746054);
}
if (!defined('MAPI_E_INVALID_ENTRYID')) {
	define('MAPI_E_INVALID_ENTRYID', 2147746055);
}
if (!defined('MAPI_E_INVALID_OBJECT')) {
	define('MAPI_E_INVALID_OBJECT', 2147746056);
}
if (!defined('MAPI_E_OBJECT_CHANGED')) {
	define('MAPI_E_OBJECT_CHANGED', 2147746057);
}
if (!defined('MAPI_E_OBJECT_DELETED')) {
	define('MAPI_E_OBJECT_DELETED', 2147746058);
}
if (!defined('MAPI_E_BUSY')) {
	define('MAPI_E_BUSY', 2147746059);
}
if (!defined('MAPI_E_NOT_ENOUGH_DISK')) {
	define('MAPI_E_NOT_ENOUGH_DISK', 2147746061);
}
if (!defined('MAPI_E_NOT_ENOUGH_RESOURCES')) {
	define('MAPI_E_NOT_ENOUGH_RESOURCES', 2147746062);
}
if (!defined('MAPI_E_NOT_FOUND')) {
	define('MAPI_E_NOT_FOUND', 2147746063);
}
if (!defined('MAPI_E_VERSION')) {
	define('MAPI_E_VERSION', 2147746064);
}
if (!defined('MAPI_E_LOGON_FAILED')) {
	define('MAPI_E_LOGON_FAILED', 2147746065);
}
if (!defined('MAPI_E_SESSION_LIMIT')) {
	define('MAPI_E_SESSION_LIMIT', 2147746066);
}
if (!defined('MAPI_E_USER_CANCEL')) {
	define('MAPI_E_USER_CANCEL', 2147746067);
}
if (!defined('MAPI_E_UNABLE_TO_ABORT')) {
	define('MAPI_E_UNABLE_TO_ABORT', 2147746068);
}
if (!defined('ecRpcFailed')) {
	define('ecRpcFailed', 2147746069);
}
if (!defined('MAPI_E_NETWORK_ERROR')) {
	define('MAPI_E_NETWORK_ERROR', 2147746069);
}
if (!defined('MAPI_E_DISK_ERROR')) {
	define('MAPI_E_DISK_ERROR', 2147746070);
}
if (!defined('MAPI_E_TOO_COMPLEX')) {
	define('MAPI_E_TOO_COMPLEX', 2147746071);
}
if (!defined('MAPI_E_BAD_COLUMN')) {
	define('MAPI_E_BAD_COLUMN', 2147746072);
}
if (!defined('MAPI_E_EXTENDED_ERROR')) {
	define('MAPI_E_EXTENDED_ERROR', 2147746073);
}
if (!defined('MAPI_E_COMPUTED')) {
	define('MAPI_E_COMPUTED', 2147746074);
}
if (!defined('MAPI_E_CORRUPT_DATA')) {
	define('MAPI_E_CORRUPT_DATA', 2147746075);
}
if (!defined('MAPI_E_UNCONFIGURED')) {
	define('MAPI_E_UNCONFIGURED', 2147746076);
}
if (!defined('MAPI_E_FAILONEPROVIDER')) {
	define('MAPI_E_FAILONEPROVIDER', 2147746077);
}
if (!defined('MAPI_E_UNKNOWN_CPID')) {
	define('MAPI_E_UNKNOWN_CPID', 2147746078);
}
if (!defined('MAPI_E_UNKNOWN_LCID')) {
	define('MAPI_E_UNKNOWN_LCID', 2147746079);
}
if (!defined('MAPI_E_PASSWORD_CHANGE_REQUIRED')) {
	define('MAPI_E_PASSWORD_CHANGE_REQUIRED', 2147746080);
}
if (!defined('MAPI_E_PASSWORD_EXPIRED')) {
	define('MAPI_E_PASSWORD_EXPIRED', 2147746081);
}
if (!defined('MAPI_E_INVALID_WORKSTATION_ACCOUNT')) {
	define('MAPI_E_INVALID_WORKSTATION_ACCOUNT', 2147746082);
}
if (!defined('MAPI_E_INVALID_ACCESS_TIME')) {
	define('MAPI_E_INVALID_ACCESS_TIME', 2147746083);
}
if (!defined('MAPI_E_ACCOUNT_DISABLED')) {
	define('MAPI_E_ACCOUNT_DISABLED', 2147746084);
}
if (!defined('MAPI_E_END_OF_SESSION')) {
	define('MAPI_E_END_OF_SESSION', 2147746304);
}
if (!defined('MAPI_E_UNKNOWN_ENTRYID')) {
	define('MAPI_E_UNKNOWN_ENTRYID', 2147746305);
}
if (!defined('MAPI_E_MISSING_REQUIRED_COLUMN')) {
	define('MAPI_E_MISSING_REQUIRED_COLUMN', 2147746306);
}
if (!defined('MAPI_E_BAD_VALUE')) {
	define('MAPI_E_BAD_VALUE', 2147746561);
}
if (!defined('MAPI_E_INVALID_TYPE')) {
	define('MAPI_E_INVALID_TYPE', 2147746562);
}
if (!defined('MAPI_E_TYPE_NO_SUPPORT')) {
	define('MAPI_E_TYPE_NO_SUPPORT', 2147746563);
}
if (!defined('MAPI_E_UNEXPECTED_TYPE')) {
	define('MAPI_E_UNEXPECTED_TYPE', 2147746564);
}
if (!defined('MAPI_E_TOO_BIG')) {
	define('MAPI_E_TOO_BIG', 2147746565);
}
if (!defined('MAPI_E_DECLINE_COPY')) {
	define('MAPI_E_DECLINE_COPY', 2147746566);
}
if (!defined('MAPI_E_UNEXPECTED_ID')) {
	define('MAPI_E_UNEXPECTED_ID', 2147746567);
}
if (!defined('MAPI_E_UNABLE_TO_COMPLETE')) {
	define('MAPI_E_UNABLE_TO_COMPLETE', 2147746816);
}
if (!defined('MAPI_E_TIMEOUT')) {
	define('MAPI_E_TIMEOUT', 2147746817);
}
if (!defined('MAPI_E_TABLE_EMPTY')) {
	define('MAPI_E_TABLE_EMPTY', 2147746818);
}
if (!defined('MAPI_E_TABLE_TOO_BIG')) {
	define('MAPI_E_TABLE_TOO_BIG', 2147746819);
}
if (!defined('MAPI_E_INVALID_BOOKMARK')) {
	define('MAPI_E_INVALID_BOOKMARK', 2147746821);
}
if (!defined('MAPI_E_WAIT')) {
	define('MAPI_E_WAIT', 2147747072);
}
if (!defined('MAPI_E_CANCEL')) {
	define('MAPI_E_CANCEL', 2147747073);
}
if (!defined('MAPI_E_NOT_ME')) {
	define('MAPI_E_NOT_ME', 2147747074);
}
if (!defined('MAPI_E_CORRUPT_STORE')) {
	define('MAPI_E_CORRUPT_STORE', 2147747328);
}
if (!defined('MAPI_E_NOT_IN_QUEUE')) {
	define('MAPI_E_NOT_IN_QUEUE', 2147747329);
}
if (!defined('MAPI_E_NO_SUPPRESS')) {
	define('MAPI_E_NO_SUPPRESS', 2147747330);
}
if (!defined('MAPI_E_COLLISION')) {
	define('MAPI_E_COLLISION', 2147747332);
}
if (!defined('MAPI_E_NOT_INITIALIZED')) {
	define('MAPI_E_NOT_INITIALIZED', 2147747333);
}
if (!defined('MAPI_E_NON_STANDARD')) {
	define('MAPI_E_NON_STANDARD', 2147747334);
}
if (!defined('MAPI_E_NO_RECIPIENTS')) {
	define('MAPI_E_NO_RECIPIENTS', 2147747335);
}
if (!defined('MAPI_E_SUBMITTED')) {
	define('MAPI_E_SUBMITTED', 2147747336);
}
if (!defined('MAPI_E_HAS_FOLDERS')) {
	define('MAPI_E_HAS_FOLDERS', 2147747337);
}
if (!defined('MAPI_E_HAS_MESSAGES')) {
	define('MAPI_E_HAS_MESSAGES', 2147747338);
}
if (!defined('MAPI_E_FOLDER_CYCLE')) {
	define('MAPI_E_FOLDER_CYCLE', 2147747339);
}
if (!defined('MAPI_E_STORE_FULL')) {
	define('MAPI_E_STORE_FULL', 2147747340);
}
if (!defined('MAPI_E_LOCKID_LIMIT')) {
	define('MAPI_E_LOCKID_LIMIT', 2147747341);
}
if (!defined('MAPI_E_AMBIGUOUS_RECIP')) {
	define('MAPI_E_AMBIGUOUS_RECIP', 2147747584);
}
if (!defined('SYNC_E_OBJECT_DELETED')) {
	define('SYNC_E_OBJECT_DELETED', 2147747840);
}
if (!defined('SYNC_E_IGNORE')) {
	define('SYNC_E_IGNORE', 2147747841);
}
if (!defined('SYNC_E_CONFLICT')) {
	define('SYNC_E_CONFLICT', 2147747842);
}
if (!defined('SYNC_E_NO_PARENT')) {
	define('SYNC_E_NO_PARENT', 2147747843);
}
if (!defined('SYNC_E_CYCLE_DETECTED')) {
	define('SYNC_E_CYCLE_DETECTED', 2147747844);
}
if (!defined('SYNC_E_CYCLE')) {
	define('SYNC_E_CYCLE', 2147747844);
}
if (!defined('SYNC_E_INCEST')) {
	define('SYNC_E_INCEST', 2147747844);
}
if (!defined('SYNC_E_UNSYNCHRONIZED')) {
	define('SYNC_E_UNSYNCHRONIZED', 2147747845);
}
if (!defined('MAPI_E_NAMED_PROP_QUOTA_EXCEEDED')) {
	define('MAPI_E_NAMED_PROP_QUOTA_EXCEEDED', 2147748096);
}
if (!defined('MAPI_E_NO_ACCESS')) {
	define('MAPI_E_NO_ACCESS', 2147942405);
}
if (!defined('MAPI_E_NOT_ENOUGH_MEMORY')) {
	define('MAPI_E_NOT_ENOUGH_MEMORY', 2147942414);
}
if (!defined('MAPI_E_INVALID_PARAMETER')) {
	define('MAPI_E_INVALID_PARAMETER', 2147942487);
}
if (!defined('SYNC_E_INVALID_PARAMETER')) {
	define('SYNC_E_INVALID_PARAMETER', 2147942487);
}
if (!defined('ecZNullObject')) {
	define('ecZNullObject', 4294966272);
}
if (!defined('ecZOutOfHandles')) {
	define('ecZOutOfHandles', 4294966276);
}
