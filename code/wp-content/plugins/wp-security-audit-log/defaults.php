<?php

// if not included correctly...
if ( !class_exists( 'WpSecurityAuditLog' ) ) exit();

// define custom / new PHP constants
defined('E_CRITICAL') || define('E_CRITICAL', 'E_CRITICAL');
defined('E_DEBUG') || define('E_DEBUG', 'E_DEBUG');
defined('E_RECOVERABLE_ERROR') || define('E_RECOVERABLE_ERROR', 'E_RECOVERABLE_ERROR');
defined('E_DEPRECATED') || define('E_DEPRECATED', 'E_DEPRECATED');
defined('E_USER_DEPRECATED') || define('E_USER_DEPRECATED', 'E_USER_DEPRECATED');

/**
 * Load Custom Alerts from uploads/wp-security-audit-log/custom-alerts.php if exists
 */
function load_include_custom_file($wsal)
{
    $upload_dir = wp_upload_dir();
    $uploadsDirPath = trailingslashit($upload_dir['basedir']) . 'wp-security-audit-log';
    // Check directory
    if (is_dir($uploadsDirPath) && is_readable($uploadsDirPath)) {
        $file = $uploadsDirPath . DIRECTORY_SEPARATOR . 'custom-alerts.php';
        if (file_exists($file)) {
            require_once($file);
            if (is_array($custom_alerts)) {
                $wsal->alerts->RegisterGroup($custom_alerts);
            }
        }
    }
}

function wsaldefaults_wsal_init(WpSecurityAuditLog $wsal)
{
    $wsal->constants->UseConstants(array(
        // default PHP constants
        array('name' => 'E_ERROR', 'description' => __('Fatal run-time error.', 'wp-security-audit-log')),
        array('name' => 'E_WARNING', 'description' => __('Run-time warning (non-fatal error).', 'wp-security-audit-log')),
        array('name' => 'E_PARSE', 'description' => __('Compile-time parse error.', 'wp-security-audit-log')),
        array('name' => 'E_NOTICE', 'description' => __('Run-time notice.', 'wp-security-audit-log')),
        array('name' => 'E_CORE_ERROR', 'description' => __('Fatal error that occurred during startup.', 'wp-security-audit-log')),
        array('name' => 'E_CORE_WARNING', 'description' => __('Warnings that occurred during startup.', 'wp-security-audit-log')),
        array('name' => 'E_COMPILE_ERROR', 'description' => __('Fatal compile-time error.', 'wp-security-audit-log')),
        array('name' => 'E_COMPILE_WARNING', 'description' => __('Compile-time warning.', 'wp-security-audit-log')),
        array('name' => 'E_USER_ERROR', 'description' => __('User-generated error message.', 'wp-security-audit-log')),
        array('name' => 'E_USER_WARNING', 'description' => __('User-generated warning message.', 'wp-security-audit-log')),
        array('name' => 'E_USER_NOTICE', 'description' => __('User-generated notice message.', 'wp-security-audit-log')),
        array('name' => 'E_STRICT', 'description' => __('Non-standard/optimal code warning.', 'wp-security-audit-log')),
        array('name' => 'E_RECOVERABLE_ERROR', 'description' => __('Catchable fatal error.', 'wp-security-audit-log')),
        array('name' => 'E_DEPRECATED', 'description' => __('Run-time deprecation notices.', 'wp-security-audit-log')),
        array('name' => 'E_USER_DEPRECATED', 'description' => __('Run-time user deprecation notices.', 'wp-security-audit-log')),
        // custom constants
        array('name' => 'E_CRITICAL', 'description' => __('Critical, high-impact messages.', 'wp-security-audit-log')),
        array('name' => 'E_DEBUG', 'description' => __('Debug informational messages.', 'wp-security-audit-log')),
    ));
    // create list of default alerts
    $wsal->alerts->RegisterGroup(array(
        __('Other User Activity', 'wp-security-audit-log') => array(
            array(1000, E_NOTICE, __('User logged in', 'wp-security-audit-log'), __('Successfully logged in.', 'wp-security-audit-log')),
            array(1001, E_NOTICE, __('User logged out', 'wp-security-audit-log'), __('Successfully logged out.', 'wp-security-audit-log')),
            array(1002, E_WARNING, __('Login failed', 'wp-security-audit-log'), __('%Attempts% failed login(s) detected.', 'wp-security-audit-log')),
            array(1003, E_WARNING, __('Login failed  / non existing user', 'wp-security-audit-log'), __('%Attempts% failed login(s) detected using non existing user.', 'wp-security-audit-log')),
            array(1004, E_WARNING, __('Login blocked', 'wp-security-audit-log'), __('Blocked from logging in because the same WordPress user is logged in from %ClientIP%.', 'wp-security-audit-log')),
            array(1005, E_WARNING, __('User logged in with existing session(s)', 'wp-security-audit-log'), __('Successfully logged in. Another session from %IPAddress% for this user already exist.', 'wp-security-audit-log')),
            array(2010, E_NOTICE, __('User uploaded file from Uploads directory', 'wp-security-audit-log'), __('Uploaded the file %FileName% in %FilePath%.', 'wp-security-audit-log')),
            array(2011, E_WARNING, __('User deleted file from Uploads directory', 'wp-security-audit-log'), __('Deleted the file %FileName% from %FilePath%.', 'wp-security-audit-log'))
        ),
        __('Blog Posts', 'wp-security-audit-log') => array(
            array(2000, E_NOTICE, __('User created a new blog post and saved it as draft', 'wp-security-audit-log'), __('Created a new post called %PostTitle% and saved it as draft. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2001, E_NOTICE, __('User published a blog post', 'wp-security-audit-log'), __('Published a post called %PostTitle%. Post URL is %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2002, E_NOTICE, __('User modified a published blog post', 'wp-security-audit-log'), __('Modified the published post %PostTitle%. Post URL is %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2003, E_NOTICE, __('User modified a draft blog post', 'wp-security-audit-log'), __('Modified the draft post with the %PostTitle%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2008, E_WARNING, __('User permanently deleted a blog post from the trash', 'wp-security-audit-log'), __('Permanently deleted the post %PostTitle%. Post URL was %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2012, E_WARNING, __('User moved a blog post to the trash', 'wp-security-audit-log'), __('Moved the post %PostTitle% to trash. Post URL is %PostUrl%.', 'wp-security-audit-log')),
            array(2014, E_CRITICAL, __('User restored a blog post from trash', 'wp-security-audit-log'), __('Post %PostTitle% has been restored from trash. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2016, E_NOTICE, __('User changed blog post category', 'wp-security-audit-log'), __('Changed the category of the post %PostTitle% from %OldCategories% to %NewCategories%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2017, E_NOTICE, __('User changed blog post URL', 'wp-security-audit-log'), __('Changed the URL of the post %PostTitle% from %OldUrl% to %NewUrl%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2019, E_NOTICE, __('User changed blog post author', 'wp-security-audit-log'), __('Changed the author of %PostTitle% post from %OldAuthor% to %NewAuthor%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2021, E_NOTICE, __('User changed blog post status', 'wp-security-audit-log'), __('Changed the status of %PostTitle% post from %OldStatus% to %NewStatus%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2023, E_NOTICE, __('User created new category', 'wp-security-audit-log'), __('Created a new category called %CategoryName% .Category slug is %Slug%. %CategoryLink%.', 'wp-security-audit-log')),
            array(2024, E_WARNING, __('User deleted category', 'wp-security-audit-log'), __('Deleted the category %CategoryName%. Category slug was %Slug%.', 'wp-security-audit-log')),
            array(2025, E_WARNING, __('User changed the visibility of a blog post', 'wp-security-audit-log'), __('Changed the visibility of the post %PostTitle% from %OldVisibility% to %NewVisibility%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2027, E_NOTICE, __('User changed the date of a blog post', 'wp-security-audit-log'), __('Changed the date of the post %PostTitle% from %OldDate% to %NewDate%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2049, E_NOTICE, __('User set a post as sticky', 'wp-security-audit-log'), __('Set the post %PostTitle% as Sticky. Post URL is %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2050, E_NOTICE, __('User removed post from sticky', 'wp-security-audit-log'), __('Removed the post %PostTitle% from Sticky. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2052, E_NOTICE, __('User changed generic tables', 'wp-security-audit-log'), __('Changed the parent of the category %CategoryName% from %OldParent% to %NewParent%. %CategoryLink%.', 'wp-security-audit-log')),
            array(2053, E_CRITICAL, __('User created a custom field for a post', 'wp-security-audit-log'), __('Created a new custom field %MetaKey% with value %MetaValue% in the post %PostTitle%'.' %EditorLinkPost%.'.'<br>%MetaLink%.', 'wp-security-audit-log')),
            array(2054, E_CRITICAL, __('User updated a custom field value for a post', 'wp-security-audit-log'), __('Modified the value of the custom field %MetaKey% from %MetaValueOld% to %MetaValueNew% in the post %PostTitle%'.' %EditorLinkPost%.'.'<br>%MetaLink%.', 'wp-security-audit-log')),
            array(2055, E_CRITICAL, __('User deleted a custom field from a post', 'wp-security-audit-log'), __('Deleted the custom field %MetaKey% with id %MetaID% from the post %PostTitle%'.' %EditorLinkPost%.'.'<br>%MetaLink%.', 'wp-security-audit-log')),
            array(2062, E_CRITICAL, __('User updated a custom field name for a post', 'wp-security-audit-log'), __('Changed the custom field name from %MetaKeyOld% to %MetaKeyNew% in the post %PostTitle%'.' %EditorLinkPost%.'.'<br>%MetaLink%.', 'wp-security-audit-log')),
            array(2065, E_WARNING, __('User modified content for a published post', 'wp-security-audit-log'), __('Modified the content of the published post %PostTitle%.'.'%RevisionLink%'.' %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2068, E_NOTICE, __('User modified content for a draft post', 'wp-security-audit-log'), __('Modified the content of the draft post %PostTitle%.'.'%RevisionLink%'.' %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2072, E_NOTICE, __('User modified content of a post', 'wp-security-audit-log'), __('Modified the content of post %PostTitle% which is submitted for review.'.'%RevisionLink%'.' %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2073, E_NOTICE, __('User submitted a post for review', 'wp-security-audit-log'), __('Submitted the post %PostTitle% for review. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2074, E_NOTICE, __('User scheduled a post', 'wp-security-audit-log'), __('Scheduled the post %PostTitle% to be published %PublishingDate%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2086, E_NOTICE, __('User changed title of a post', 'wp-security-audit-log'), __('Changed the title of the post %OldTitle% to %NewTitle%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2100, E_NOTICE, __('User opened a post in the editor', 'wp-security-audit-log'), __('Opened the post %PostTitle% in the editor. View the post: %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2101, E_NOTICE, __('User viewed a post', 'wp-security-audit-log'), __('Viewed the post %PostTitle%. View the post: %PostUrl%.', 'wp-security-audit-log'))
        ),
        __('Pages', 'wp-security-audit-log') => array(
            array(2004, E_NOTICE, __('User created a new WordPress page and saved it as draft', 'wp-security-audit-log'), __('Created a new page called %PostTitle% and saved it as draft. %EditorLinkPage%.', 'wp-security-audit-log')),
            array(2005, E_NOTICE, __('User published a WordPress page', 'wp-security-audit-log'), __('Published a page called %PostTitle%. Page URL is %PostUrl%. %EditorLinkPage%.', 'wp-security-audit-log')),
            array(2006, E_NOTICE, __('User modified a published WordPress page', 'wp-security-audit-log'), __('Modified the published page %PostTitle%. Page URL is %PostUrl%. %EditorLinkPage%.', 'wp-security-audit-log')),
            array(2007, E_NOTICE, __('User modified a draft WordPress page', 'wp-security-audit-log'), __('Modified the draft page %PostTitle%. Page ID is %PostID%. %EditorLinkPage%.', 'wp-security-audit-log')),
            array(2009, E_WARNING, __('User permanently deleted a page from the trash', 'wp-security-audit-log'), __('Permanently deleted the page %PostTitle%. Page URL was %PostUrl%. %EditorLinkPage%.', 'wp-security-audit-log')),
            array(2013, E_WARNING, __('User moved WordPress page to the trash', 'wp-security-audit-log'), __('Moved the page %PostTitle% to trash. Page URL was %PostUrl%.', 'wp-security-audit-log')),
            array(2015, E_CRITICAL, __('User restored a WordPress page from trash', 'wp-security-audit-log'), __('Page %PostTitle% has been restored from trash. %EditorLinkPage%.', 'wp-security-audit-log')),
            array(2018, E_NOTICE, __('User changed page URL', 'wp-security-audit-log'), __('Changed the URL of the page %PostTitle% from %OldUrl% to %NewUrl%. %EditorLinkPage%.', 'wp-security-audit-log')),
            array(2020, E_NOTICE, __('User changed page author', 'wp-security-audit-log'), __('Changed the author of the page %PostTitle% from %OldAuthor% to %NewAuthor%. %EditorLinkPage%.', 'wp-security-audit-log')),
            array(2022, E_NOTICE, __('User changed page status', 'wp-security-audit-log'), __('Changed the status of the page %PostTitle% from %OldStatus% to %NewStatus%. %EditorLinkPage%.', 'wp-security-audit-log')),
            array(2026, E_WARNING, __('User changed the visibility of a page post', 'wp-security-audit-log'), __('Changed the visibility of the page %PostTitle% from %OldVisibility% to %NewVisibility%. %EditorLinkPage%.', 'wp-security-audit-log')),
            array(2028, E_NOTICE, __('User changed the date of a page post', 'wp-security-audit-log'), __('Changed the date of the page %PostTitle% from %OldDate% to %NewDate%. %EditorLinkPage%.', 'wp-security-audit-log')),
            array(2047, E_NOTICE, __('User changed the parent of a page', 'wp-security-audit-log'), __('Changed the parent of the page %PostTitle% from %OldParentName% to %NewParentName%. %EditorLinkPage%.', 'wp-security-audit-log')),
            array(2048, E_CRITICAL, __('User changed the template of a page', 'wp-security-audit-log'), __('Changed the template of the page %PostTitle% from %OldTemplate% to %NewTemplate%. %EditorLinkPage%.', 'wp-security-audit-log')),
            array(2059, E_CRITICAL, __('User created a custom field for a page', 'wp-security-audit-log'), __('Created a new custom field called %MetaKey% with value %MetaValue% in the page %PostTitle%'.' %EditorLinkPage%.'.'<br>%MetaLink%.', 'wp-security-audit-log')),
            array(2060, E_CRITICAL, __('User updated a custom field value for a page', 'wp-security-audit-log'), __('Modified the value of the custom field %MetaKey% from %MetaValueOld% to %MetaValueNew% in the page %PostTitle%'.' %EditorLinkPage%.'.'<br>%MetaLink%.', 'wp-security-audit-log')),
            array(2061, E_CRITICAL, __('User deleted a custom field from a page', 'wp-security-audit-log'), __('Deleted the custom field %MetaKey% with id %MetaID% from page %PostTitle%'.' %EditorLinkPage%.'.'<br>%MetaLink%.', 'wp-security-audit-log')),
            array(2064, E_CRITICAL, __('User updated a custom field name for a page', 'wp-security-audit-log'), __('Changed the custom field name from %MetaKeyOld% to %MetaKeyNew% in the page %PostTitle%'.' %EditorLinkPage%.'.'<br>%MetaLink%.', 'wp-security-audit-log')),
            array(2066, E_WARNING, __('User modified content for a published page', 'wp-security-audit-log'), __('Modified the content of the published page %PostTitle%. Page URL is %PostUrl%.'.'%RevisionLink%'.' %EditorLinkPage%.', 'wp-security-audit-log')),
            array(2069, E_NOTICE, __('User modified content for a draft page', 'wp-security-audit-log'), __('Modified the content of draft page %PostTitle%.'.'%RevisionLink%'.' %EditorLinkPage%.', 'wp-security-audit-log')),
            array(2075, E_NOTICE, __('User scheduled a page', 'wp-security-audit-log'), __('Scheduled the page %PostTitle% to be published %PublishingDate%.'.' %EditorLinkPage%.', 'wp-security-audit-log')),
            array(2087, E_NOTICE, __('User changed title of a page', 'wp-security-audit-log'), __('Changed the title of the page %OldTitle% to %NewTitle%.'.' %EditorLinkPage%.', 'wp-security-audit-log')),
            array(2102, E_NOTICE, __('User opened a page in the editor', 'wp-security-audit-log'), __('Opened the page %PostTitle% in the editor. View the page: %EditorLinkPage%.', 'wp-security-audit-log')),
            array(2103, E_NOTICE, __('User viewed a page', 'wp-security-audit-log'), __('Viewed the page %PostTitle%. View the page: %PostUrl%.', 'wp-security-audit-log'))
        ),
        __('Custom Posts', 'wp-security-audit-log') => array(
            array(2029, E_NOTICE, __('User created a new post with custom post type and saved it as draft', 'wp-security-audit-log'), __('Created a new custom post called %PostTitle% of type %PostType%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2030, E_NOTICE, __('User published a post with custom post type', 'wp-security-audit-log'), __('Published a custom post %PostTitle% of type %PostType%. Post URL is %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2031, E_NOTICE, __('User modified a post with custom post type', 'wp-security-audit-log'), __('Modified the custom post %PostTitle% of type %PostType%. Post URL is %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2032, E_NOTICE, __('User modified a draft post with custom post type', 'wp-security-audit-log'), __('Modified the draft custom post %PostTitle% of type is %PostType%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2033, E_WARNING, __('User permanently deleted post with custom post type', 'wp-security-audit-log'), __('Permanently Deleted the custom post %PostTitle% of type %PostType%. The post URL was %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2034, E_WARNING, __('User moved post with custom post type to trash', 'wp-security-audit-log'), __('Moved the custom post %PostTitle% of type %PostType% to trash. Post URL was %PostUrl%.', 'wp-security-audit-log')),
            array(2035, E_CRITICAL, __('User restored post with custom post type from trash', 'wp-security-audit-log'), __('The custom post %PostTitle% of type %PostType% has been restored from trash. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2036, E_NOTICE, __('User changed the category of a post with custom post type', 'wp-security-audit-log'), __('Changed the category(ies) of the custom post %PostTitle% of type %PostType% from %OldCategories% to %NewCategories%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2037, E_NOTICE, __('User changed the URL of a post with custom post type', 'wp-security-audit-log'), __('Changed the URL of the custom post %PostTitle% of type %PostType% from %OldUrl% to %NewUrl%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2038, E_NOTICE, __('User changed the author or post with custom post type', 'wp-security-audit-log'), __('Changed the author of custom post %PostTitle% of type %PostType% from %OldAuthor% to %NewAuthor%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2039, E_NOTICE, __('User changed the status of post with custom post type', 'wp-security-audit-log'), __('Changed the status of custom post %PostTitle% of type %PostType% from %OldStatus% to %NewStatus%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2040, E_WARNING, __('User changed the visibility of a post with custom post type', 'wp-security-audit-log'), __('Changed the visibility of the custom post %PostTitle% of type %PostType% from %OldVisibility% to %NewVisibility%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2041, E_NOTICE, __('User changed the date of post with custom post type', 'wp-security-audit-log'), __('Changed the date of the custom post %PostTitle% of type %PostType% from %OldDate% to %NewDate%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2056, E_CRITICAL, __('User created a custom field for a custom post type', 'wp-security-audit-log'), __('Created a new custom field %MetaKey% with value %MetaValue% in custom post %PostTitle% of type %PostType%.'.' %EditorLinkPost%.'.'<br>%MetaLink%.', 'wp-security-audit-log')),
            array(2057, E_CRITICAL, __('User updated a custom field for a custom post type', 'wp-security-audit-log'), __('Modified the value of the custom field %MetaKey% from %MetaValueOld% to %MetaValueNew% in custom post %PostTitle% of type %PostType%'.' %EditorLinkPost%.'.'<br>%MetaLink%.', 'wp-security-audit-log')),
            array(2058, E_CRITICAL, __('User deleted a custom field from a custom post type', 'wp-security-audit-log'), __('Deleted the custom field %MetaKey% with id %MetaID% from custom post %PostTitle% of type %PostType%'.' %EditorLinkPost%.'.'<br>%MetaLink%.', 'wp-security-audit-log')),
            array(2063, E_CRITICAL, __('User updated a custom field name for a custom post type', 'wp-security-audit-log'), __('Changed the custom field name from %MetaKeyOld% to %MetaKeyNew% in custom post %PostTitle% of type %PostType%'.' %EditorLinkPost%.'.'<br>%MetaLink%.', 'wp-security-audit-log')),
            array(2067, E_WARNING, __('User modified content for a published custom post type', 'wp-security-audit-log'), __('Modified the content of the published custom post type %PostTitle%. Post URL is %PostUrl%.'.'%EditorLinkPost%.', 'wp-security-audit-log')),
            array(2070, E_NOTICE, __('User modified content for a draft custom post type', 'wp-security-audit-log'), __('Modified the content of the draft custom post type %PostTitle%.'.'%EditorLinkPost%.', 'wp-security-audit-log')),
            array(2076, E_NOTICE, __('User scheduled a custom post type', 'wp-security-audit-log'), __('Scheduled the custom post type %PostTitle% to be published %PublishingDate%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2088, E_NOTICE, __('User changed title of a custom post type', 'wp-security-audit-log'), __('Changed the title of the custom post %OldTitle% to %NewTitle%. %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2104, E_NOTICE, __('User opened a custom post type in the editor', 'wp-security-audit-log'), __('Opened the custom post %PostTitle% of type %PostType% in the editor. View the post: %EditorLinkPost%.', 'wp-security-audit-log')),
            array(2105, E_NOTICE, __('User viewed a custom post type', 'wp-security-audit-log'), __('Viewed the custom post %PostTitle% of type %PostType%. View the post: %PostUrl%.', 'wp-security-audit-log'))
        ),
        __('Widgets', 'wp-security-audit-log') => array(
            array(2042, E_CRITICAL, __('User added a new widget', 'wp-security-audit-log'), __('Added a new %WidgetName% widget in  %Sidebar%.', 'wp-security-audit-log')),
            array(2043, E_WARNING, __('User modified a widget', 'wp-security-audit-log'), __('Modified the %WidgetName% widget in %Sidebar%.', 'wp-security-audit-log')),
            array(2044, E_CRITICAL, __('User deleted widget', 'wp-security-audit-log'), __('Deleted the %WidgetName% widget from %Sidebar%.', 'wp-security-audit-log')),
            array(2045, E_NOTICE, __('User moved widget', 'wp-security-audit-log'), __('Moved the %WidgetName% widget from %OldSidebar% to %NewSidebar%.', 'wp-security-audit-log')),
            array(2071, E_NOTICE, __('User changed widget position', 'wp-security-audit-log'), __('Changed the position of the widget %WidgetName% in sidebar %Sidebar%.', 'wp-security-audit-log'))
        ),
        __('User Profiles', 'wp-security-audit-log') => array(
            array(4000, E_CRITICAL, __('New user was created on WordPress', 'wp-security-audit-log'), __('A new user %NewUserData->Username% was created with role of %NewUserData->Roles%.', 'wp-security-audit-log')),
            array(4001, E_CRITICAL, __('User created another WordPress user', 'wp-security-audit-log'), __('%UserChanger% created a new user %NewUserData->Username% with the role of %NewUserData->Roles%.', 'wp-security-audit-log')),
            array(4002, E_CRITICAL, __('The role of a user was changed by another WordPress user', 'wp-security-audit-log'), __('Changed the role of the user %TargetUsername% from %OldRole% to %NewRole%.', 'wp-security-audit-log')),
            array(4003, E_CRITICAL, __('User has changed his or her password', 'wp-security-audit-log'), __('Changed the password.', 'wp-security-audit-log')),
            array(4004, E_CRITICAL, __('User changed another user\'s password', 'wp-security-audit-log'), __('Changed the password for the user %TargetUserData->Username% with the role of %TargetUserData->Roles%.', 'wp-security-audit-log')),
            array(4005, E_NOTICE, __('User changed his or her email address', 'wp-security-audit-log'), __('Changed the email address from %OldEmail% to %NewEmail%.', 'wp-security-audit-log')),
            array(4006, E_NOTICE, __('User changed another user\'s email address', 'wp-security-audit-log'), __('Changed the email address of the user %TargetUsername% from %OldEmail% to %NewEmail%.', 'wp-security-audit-log')),
            array(4007, E_CRITICAL, __('User was deleted by another user', 'wp-security-audit-log'), __('Deleted the user %TargetUserData->Username% with the role of %TargetUserData->Roles%.', 'wp-security-audit-log'))
        ),
        __('Plugins & Themes', 'wp-security-audit-log') => array(
            array(5000, E_CRITICAL, __('User installed a plugin', 'wp-security-audit-log'), __('Installed the plugin %Plugin->Name% in %Plugin->plugin_dir_path%.', 'wp-security-audit-log')),
            array(5001, E_CRITICAL, __('User activated a WordPress plugin', 'wp-security-audit-log'), __('Activated the plugin %PluginData->Name% installed in %PluginFile%.', 'wp-security-audit-log')),
            array(5002, E_CRITICAL, __('User deactivated a WordPress plugin', 'wp-security-audit-log'), __('Deactivated the plugin %PluginData->Name% installed in %PluginFile%.', 'wp-security-audit-log')),
            array(5003, E_CRITICAL, __('User uninstalled a plugin', 'wp-security-audit-log'), __('Uninstalled the plugin %PluginData->Name% which was installed in %PluginFile%.', 'wp-security-audit-log')),
            array(5004, E_WARNING, __('User upgraded a plugin', 'wp-security-audit-log'), __('Upgraded the plugin %PluginData->Name% installed in %PluginFile%.', 'wp-security-audit-log')),
            array(5005, E_WARNING, __('User installed a theme', 'wp-security-audit-log'), __('Installed the theme "%Theme->Name%" in %Theme->get_template_directory%.', 'wp-security-audit-log')),
            array(5006, E_CRITICAL, __('User activated a theme', 'wp-security-audit-log'), __('Activated the theme "%Theme->Name%", installed in %Theme->get_template_directory%.', 'wp-security-audit-log')),
            array(5007, E_CRITICAL, __('User uninstalled a theme', 'wp-security-audit-log'), __('Deleted the theme "%Theme->Name%" installed in %Theme->get_template_directory%.', 'wp-security-audit-log')),
            array(5019, E_CRITICAL, __('A plugin created a post', 'wp-security-audit-log'), __('A plugin automatically created the following post: %PostTitle%.', 'wp-security-audit-log')),
            array(5020, E_CRITICAL, __('A plugin created a page', 'wp-security-audit-log'), __('A plugin automatically created the following page: %PostTitle%.', 'wp-security-audit-log')),
            array(5021, E_CRITICAL, __('A plugin created a custom post', 'wp-security-audit-log'), __('A plugin automatically created the following custom post: %PostTitle%.', 'wp-security-audit-log')),
            array(5025, E_CRITICAL, __('A plugin deleted a post', 'wp-security-audit-log'), __('A plugin automatically deleted the following post: %PostTitle%.', 'wp-security-audit-log')),
            array(5026, E_CRITICAL, __('A plugin deleted a page', 'wp-security-audit-log'), __('A plugin automatically deleted the following page: %PostTitle%.', 'wp-security-audit-log')),
            array(5027, E_CRITICAL, __('A plugin deleted a custom post', 'wp-security-audit-log'), __('A plugin automatically deleted the following custom post: %PostTitle%.', 'wp-security-audit-log')),
            array(5031, E_WARNING, __('User updated a theme', 'wp-security-audit-log'), __('Updated the theme "%Theme->Name%" installed in %Theme->get_template_directory%.', 'wp-security-audit-log')),
            array(2046, E_CRITICAL, __('User changed a file using the theme editor', 'wp-security-audit-log'), __('Modified %File% with the Theme Editor.', 'wp-security-audit-log')),
            array(2051, E_CRITICAL, __('User changed a file using the plugin editor', 'wp-security-audit-log'), __('Modified %File% with the Plugin Editor.', 'wp-security-audit-log'))
        ),
        __('System Activity', 'wp-security-audit-log') => array(
            array(0000, E_CRITICAL, __('Unknown Error', 'wp-security-audit-log'), __('An unexpected error has occurred .', 'wp-security-audit-log')),
            array(0001, E_CRITICAL, __('PHP error', 'wp-security-audit-log'), __('%Message%.', 'wp-security-audit-log')),
            array(0002, E_WARNING, __('PHP warning', 'wp-security-audit-log'), __('%Message%.', 'wp-security-audit-log')),
            array(0003, E_NOTICE, __('PHP notice', 'wp-security-audit-log'), __('%Message%.', 'wp-security-audit-log')),
            array(0004, E_CRITICAL, __('PHP exception', 'wp-security-audit-log'), __('%Message%.', 'wp-security-audit-log')),
            array(0005, E_CRITICAL, __('PHP shutdown error', 'wp-security-audit-log'), __('%Message%.', 'wp-security-audit-log')),
            array(6000, E_NOTICE, __('Events automatically pruned by system', 'wp-security-audit-log'), __('System automatically deleted %EventCount% alert(s).', 'wp-security-audit-log')),
            array(6001, E_CRITICAL, __('Option Anyone Can Register in WordPress settings changed', 'wp-security-audit-log'), __('%NewValue% the option "Anyone can register".', 'wp-security-audit-log')),
            array(6002, E_CRITICAL, __('New User Default Role changed', 'wp-security-audit-log'), __('Changed the New User Default Role from %OldRole% to %NewRole%.', 'wp-security-audit-log')),
            array(6003, E_CRITICAL, __('WordPress Administrator Notification email changed', 'wp-security-audit-log'), __('Changed the WordPress administrator notifications email address from %OldEmail% to %NewEmail%.', 'wp-security-audit-log')),
            array(6004, E_CRITICAL, __('WordPress was updated', 'wp-security-audit-log'), __('Updated WordPress from version %OldVersion% to %NewVersion%.', 'wp-security-audit-log')),
            array(6005, E_CRITICAL, __('User changes the WordPress Permalinks', 'wp-security-audit-log'), __('Changed the WordPress permalinks from %OldPattern% to %NewPattern%.', 'wp-security-audit-log')),
            array(6007, E_CRITICAL, __('User requests non-existing pages (404 Error Pages)', 'wp-security-audit-log'), __('Has requested a non existing page (404 Error Pages) %Attempts% %Msg%. %LinkFile%.', 'wp-security-audit-log')),
            array(9999, E_CRITICAL, __('Advertising Add-ons.', 'wp-security-audit-log'), __('%PromoName% %PromoMessage%', 'wp-security-audit-log'))
        ),
        __('MultiSite', 'wp-security-audit-log') => array(
            array(4008, E_CRITICAL, __('User granted Super Admin privileges', 'wp-security-audit-log'), __('Granted Super Admin privileges to %TargetUsername%.', 'wp-security-audit-log')),
            array(4009, E_CRITICAL, __('User revoked from Super Admin privileges', 'wp-security-audit-log'), __('Revoked Super Admin privileges from %TargetUsername%.', 'wp-security-audit-log')),
            array(4010, E_CRITICAL, __('Existing user added to a site', 'wp-security-audit-log'), __('Added the existing user %TargetUsername% with %TargetUserRole% role to site %SiteName%.', 'wp-security-audit-log')),
            array(4011, E_CRITICAL, __('User removed from site', 'wp-security-audit-log'), __('Removed the user %TargetUsername% with role %TargetUserRole% from %SiteName% site.', 'wp-security-audit-log')),
            array(4012, E_CRITICAL, __('New network user created', 'wp-security-audit-log'), __('Created a new network user %NewUserData->Username%.', 'wp-security-audit-log')),
            array(4013, E_CRITICAL, __('The forum role of a user was changed by another WordPress user', 'wp-security-audit-log'), __('Change the forum role of the user %TargetUsername% from %OldRole% to %NewRole% by %UserChanger%.', 'wp-security-audit-log')),
            array(7000, E_CRITICAL, __('New site added on the network', 'wp-security-audit-log'), __('Added the site %SiteName% to the network.', 'wp-security-audit-log')),
            array(7001, E_CRITICAL, __('Existing site archived', 'wp-security-audit-log'), __('Archived the site %SiteName%.', 'wp-security-audit-log')),
            array(7002, E_CRITICAL, __('Archived site has been unarchived', 'wp-security-audit-log'), __('Unarchived the site %SiteName%.', 'wp-security-audit-log')),
            array(7003, E_CRITICAL, __('Deactivated site has been activated', 'wp-security-audit-log'), __('Activated the site %SiteName%.', 'wp-security-audit-log')),
            array(7004, E_CRITICAL, __('Site has been deactivated', 'wp-security-audit-log'), __('Deactivated the site %SiteName%.', 'wp-security-audit-log')),
            array(7005, E_CRITICAL, __('Existing site deleted from network', 'wp-security-audit-log'), __('Deleted the site %SiteName%.', 'wp-security-audit-log')),
            array(5008, E_CRITICAL, __('Activated theme on network', 'wp-security-audit-log'), __('Network activated the theme %Theme->Name% installed in %Theme->get_template_directory%.', 'wp-security-audit-log')),
            array(5009, E_CRITICAL, __('Deactivated theme from network', 'wp-security-audit-log'), __('Network deactivated the theme %Theme->Name% installed in %Theme->get_template_directory%.', 'wp-security-audit-log'))
        ),
        __('Database', 'wp-security-audit-log') => array(
            array(5010, E_CRITICAL, __('Plugin created tables', 'wp-security-audit-log'), __('Plugin %Plugin->Name% created these tables in the database: %TableNames%.', 'wp-security-audit-log')),
            array(5011, E_CRITICAL, __('Plugin modified tables structure', 'wp-security-audit-log'), __('Plugin %Plugin->Name% modified the structure of these database tables: %TableNames%.', 'wp-security-audit-log')),
            array(5012, E_CRITICAL, __('Plugin deleted tables', 'wp-security-audit-log'), __('Plugin %Plugin->Name% deleted the following tables from the database: %TableNames%.', 'wp-security-audit-log')),
            array(5013, E_CRITICAL, __('Theme created tables', 'wp-security-audit-log'), __('Theme %Theme->Name% created these tables in the database: %TableNames%.', 'wp-security-audit-log')),
            array(5014, E_CRITICAL, __('Theme modified tables structure', 'wp-security-audit-log'), __('Theme %Theme->Name% modified the structure of these database tables: %TableNames%.', 'wp-security-audit-log')),
            array(5015, E_CRITICAL, __('Theme deleted tables', 'wp-security-audit-log'), __('Theme %Theme->Name% deleted the following tables from the database: %TableNames%.', 'wp-security-audit-log')),
            array(5016, E_CRITICAL, __('Unknown component created tables', 'wp-security-audit-log'), __('An unknown component created these tables in the database: %TableNames%.', 'wp-security-audit-log')),
            array(5017, E_CRITICAL, __('Unknown component modified tables structure', 'wp-security-audit-log'), __('An unknown component modified the structure of these database tables: %TableNames%.', 'wp-security-audit-log')),
            array(5018, E_CRITICAL, __('Unknown component deleted tables', 'wp-security-audit-log'), __('An unknown component deleted the following tables from the database: %TableNames%.', 'wp-security-audit-log'))
        ),
        __('BBPress Forum', 'wp-security-audit-log') => array(
            array(8000, E_CRITICAL, __('User created new forum', 'wp-security-audit-log'), __('Created new forum %ForumName%. Forum URL is %ForumURL%.'.' %EditorLinkForum%.', 'wp-security-audit-log')),
            array(8001, E_NOTICE, __('User changed status of a forum', 'wp-security-audit-log'), __('Changed the status of the forum %ForumName% from %OldStatus% to %NewStatus%.'.' %EditorLinkForum%.', 'wp-security-audit-log')),
            array(8002, E_NOTICE, __('User changed visibility of a forum', 'wp-security-audit-log'), __('Changed the visibility of the forum %ForumName% from %OldVisibility% to %NewVisibility%.'.' %EditorLinkForum%.', 'wp-security-audit-log')),
            array(8003, E_CRITICAL, __('User changed the URL of a forum', 'wp-security-audit-log'), __('Changed the URL of the forum %ForumName% from %OldUrl% to %NewUrl%.'.' %EditorLinkForum%.', 'wp-security-audit-log')),
            array(8004, E_NOTICE, __('User changed order of a forum', 'wp-security-audit-log'), __('Changed the order of the forum %ForumName% from %OldOrder% to %NewOrder%.'.' %EditorLinkForum%.', 'wp-security-audit-log')),
            array(8005, E_CRITICAL, __('User moved forum to trash', 'wp-security-audit-log'), __('Moved the forum %ForumName% to trash.', 'wp-security-audit-log')),
            array(8006, E_WARNING, __('User permanently deleted forum', 'wp-security-audit-log'), __('Permanently deleted the forum %ForumName%.', 'wp-security-audit-log')),
            array(8007, E_WARNING, __('User restored forum from trash', 'wp-security-audit-log'), __('Restored the forum %ForumName% from trash.'.' %EditorLinkForum%.', 'wp-security-audit-log')),
            array(8008, E_NOTICE, __('User changed the parent of a forum', 'wp-security-audit-log'), __('Changed the parent of the forum %ForumName% from %OldParent% to %NewParent%.'.' %EditorLinkForum%.', 'wp-security-audit-log')),
            array(8009, E_WARNING, __('User changed forum\'s role', 'wp-security-audit-log'), __('Changed the forum\'s auto role from %OldRole% to %NewRole%.', 'wp-security-audit-log')),
            array(8010, E_WARNING, __('User changed option of a forum', 'wp-security-audit-log'), __('%Status% the option for anonymous posting on forum.', 'wp-security-audit-log')),
            array(8011, E_NOTICE, __('User changed type of a forum', 'wp-security-audit-log'), __('Changed the type of the forum %ForumName% from %OldType% to %NewType%.'.' %EditorLinkForum%.', 'wp-security-audit-log')),
            array(8012, E_NOTICE, __('User changed time to disallow post editing', 'wp-security-audit-log'), __('Changed the time to disallow post editing from %OldTime% to %NewTime% minutes in the forums.', 'wp-security-audit-log')),
            array(8013, E_WARNING, __('User changed the forum setting posting throttle time', 'wp-security-audit-log'), __('Changed the posting throttle time from %OldTime% to %NewTime% seconds in the forums.', 'wp-security-audit-log')),
            array(8014, E_NOTICE, __('User created new topic', 'wp-security-audit-log'), __('Created a new topic %TopicName%.'.' %EditorLinkTopic%.', 'wp-security-audit-log')),
            array(8015, E_NOTICE, __('User changed status of a topic', 'wp-security-audit-log'), __('Changed the status of the topic %TopicName% from %OldStatus% to %NewStatus%.'.' %EditorLinkTopic%.', 'wp-security-audit-log')),
            array(8016, E_NOTICE, __('User changed type of a topic', 'wp-security-audit-log'), __('Changed the type of the topic %TopicName% from %OldType% to %NewType%.'.' %EditorLinkTopic%.', 'wp-security-audit-log')),
            array(8017, E_CRITICAL, __('User changed URL of a topic', 'wp-security-audit-log'), __('Changed the URL of the topic %TopicName% from %OldUrl% to %NewUrl%.', 'wp-security-audit-log')),
            array(8018, E_NOTICE, __('User changed the forum of a topic', 'wp-security-audit-log'), __('Changed the forum of the topic %TopicName% from %OldForum% to %NewForum%.'.' %EditorLinkTopic%.', 'wp-security-audit-log')),
            array(8019, E_CRITICAL, __('User moved topic to trash', 'wp-security-audit-log'), __('Moved the topic %TopicName% to trash.', 'wp-security-audit-log')),
            array(8020, E_WARNING, __('User permanently deleted topic', 'wp-security-audit-log'), __('Permanently deleted the topic %TopicName%.', 'wp-security-audit-log')),
            array(8021, E_WARNING, __('User restored topic from trash', 'wp-security-audit-log'), __('Restored the topic %TopicName% from trash.'.' %EditorLinkTopic%.', 'wp-security-audit-log')),
            array(8022, E_NOTICE, __('User changed visibility of a topic', 'wp-security-audit-log'), __('Changed the visibility of the topic %TopicName% from %OldVisibility% to %NewVisibility%.'.' %EditorLinkTopic%.', 'wp-security-audit-log'))
        ),
        __('Menus', 'wp-security-audit-log') => array(
            array(2078, E_NOTICE, __('User created new menu', 'wp-security-audit-log'), __('Created a new menu called %MenuName%.', 'wp-security-audit-log')),
            array(2079, E_WARNING, __('User added content to a menu', 'wp-security-audit-log'), __('Added the %ContentType% called %ContentName% to menu %MenuName%.', 'wp-security-audit-log')),
            array(2080, E_WARNING, __('User removed content from a menu', 'wp-security-audit-log'), __('Removed the %ContentType% called %ContentName% from the menu %MenuName%.', 'wp-security-audit-log')),
            array(2081, E_CRITICAL, __('User deleted menu', 'wp-security-audit-log'), __('Deleted the menu %MenuName%.', 'wp-security-audit-log')),
            array(2082, E_WARNING, __('User changed menu setting', 'wp-security-audit-log'), __('%Status% the menu setting %MenuSetting% in %MenuName%.', 'wp-security-audit-log')),
            array(2083, E_NOTICE, __('User modified content in a menu', 'wp-security-audit-log'), __('Modified the %ContentType% called %ContentName% in menu %MenuName%.', 'wp-security-audit-log')),
            array(2084, E_WARNING, __('User changed name of a menu', 'wp-security-audit-log'), __('Changed the name of menu %OldMenuName% to %NewMenuName%.', 'wp-security-audit-log')),
            array(2085, E_NOTICE, __('User changed order of the objects in a menu', 'wp-security-audit-log'), __('Changed the order of the %ItemName% in menu %MenuName%.', 'wp-security-audit-log')),
            array(2089, E_NOTICE, __('User moved objects as a sub-item', 'wp-security-audit-log'), __('Moved %ItemName% as a sub-item of %ParentName% in menu %MenuName%.', 'wp-security-audit-log'))
        ),
        __('Comments', 'wp-security-audit-log') => array(
            array(2090, E_NOTICE, __('User approved a comment', 'wp-security-audit-log'), __('Approved the comment posted in response to the post %PostTitle% by %Author% on %CommentLink%.', 'wp-security-audit-log')),
            array(2091, E_NOTICE, __('User unapproved a comment', 'wp-security-audit-log'), __('Unapproved the comment posted in response to the post %PostTitle% by %Author% on %CommentLink%.', 'wp-security-audit-log')),
            array(2092, E_NOTICE, __('User replied to a comment', 'wp-security-audit-log'), __('Replied to the comment posted in response to the post %PostTitle% by %Author% on %CommentLink%.', 'wp-security-audit-log')),
            array(2093, E_NOTICE, __('User edited a comment', 'wp-security-audit-log'), __('Edited a comment posted in response to the post %PostTitle% by %Author% on %CommentLink%.', 'wp-security-audit-log')),
            array(2094, E_NOTICE, __('User marked a comment as Spam', 'wp-security-audit-log'), __('Marked the comment posted in response to the post %PostTitle% by %Author% on %CommentLink% as Spam.', 'wp-security-audit-log')),
            array(2095, E_NOTICE, __('User marked a comment as Not Spam', 'wp-security-audit-log'), __('Marked the comment posted in response to the post %PostTitle% by %Author% on %CommentLink% as Not Spam.', 'wp-security-audit-log')),
            array(2096, E_NOTICE, __('User moved a comment to trash', 'wp-security-audit-log'), __('Moved the comment posted in response to the post %PostTitle% by %Author% on %Date% to trash.', 'wp-security-audit-log')),
            array(2097, E_NOTICE, __('User restored a comment from the trash', 'wp-security-audit-log'), __('Restored the comment posted in response to the post %PostTitle% by %Author% on %CommentLink% from the trash.', 'wp-security-audit-log')),
            array(2098, E_NOTICE, __('User permanently deleted a comment', 'wp-security-audit-log'), __('Permanently deleted the comment posted in response to the post %PostTitle% by %Author% on %Date%.', 'wp-security-audit-log')),
            array(2099, E_NOTICE, __('User posted a comment', 'wp-security-audit-log'), __('%CommentMsg% on %CommentLink%.', 'wp-security-audit-log'))
        )
    ));
    // Load Custom alerts
    load_include_custom_file($wsal);
}
add_action('wsal_init', 'wsaldefaults_wsal_init');
