<?php
namespace WRDSB\Lamson\Views;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/wrdsb
 * @since      1.0.0
 *
 * @package    WRDSB_Lamson
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WRDSB_Lamson
 * @author     WRDSB <website@wrdsb.ca>
 */
class PostEdit
{
    /**
     * Someting
     *
     * Something else
     *
     * @since    1.0.0
     */
    public static function addMetaBoxes($meta_boxes)
    {
        $prefix = 'lamson_';
        $lamson_features = (LAMSON_FEATURE_FLAGS) ? LAMSON_FEATURE_FLAGS : array();
    
        $fields = array();
        if ($lamson_features["email_notification_toggle"]) {
            $fields[] = self::sendNotificationToggle($prefix);
        }
        if ($lamson_features["post_syndication_options"] || $lamson_features["test_syndication_options"]) {
            $fields[] = self::syndicationToggle($prefix);
        }
        if ($lamson_features["post_syndication_options"]) {
            $fields[] = self::miscSyndicationOptions($prefix);
            $fields[] = self::secondarySchoolOptions($prefix);
            $fields[] = self::elementarySchoolOptions($prefix);
        }
        if ($lamson_features["test_syndication_options"]) {
            $fields[] = self::testSyndicationOptions($prefix);
        }
    
        $meta_boxes[] = array(
            'id' => 'notificationsandsyndication',
            'title' => esc_html__('Notifications and Syndication', 'default'),
            'post_types' => array('post'),
            'context' => 'after_editor',
            'priority' => 'default',
            'autosave' => 'true',
            'fields' => $fields,
        );
    
        return $meta_boxes;
    }

    public static function sendNotificationToggle($prefix)
    {
        return array(
            'id' => $prefix . 'send_notification',
            'name' => esc_html__('Notify email subscribers?', 'default'),
            'type' => 'radio',
            'desc' => esc_html__('When the "Publish" (or "Update") button is pressed, send a copy of this post to this site\'s email notification subscribers.', 'default'),
            'placeholder' => '',
            'options' => array(
                'yes' => 'Yes',
                'no' => 'No',
            ),
            'inline' => 'true',
            'std' => 'yes',
        );
    }
    
    public static function syndicationToggle($prefix)
    {
        return array(
            'id' => $prefix . 'do_syndication',
            'name' => esc_html__('Syndicate post?', 'default'),
            'type' => 'radio',
            'desc' => esc_html__('When the "Publish" (or "Update") button is pressed, syndicate a copy of this post to the sites below.', 'default'),
            'placeholder' => '',
            'options' => array(
                'yes' => 'Yes',
                'no' => 'No',
            ),
            'inline' => 'true',
            'std' => 'yes',
        );
    }
    
    public static function miscSyndicationOptions($prefix)
    {
        return array(
            'id'      => $prefix . 'syndication_targets',
            'name'    => 'Schools',
            'type'    => 'checkbox_list',
            // Options of checkboxes, in format 'value' => 'Label'
            'options' => array(
                'schools-all'        => 'All Schools',
                'schools-elementary' => 'Elementary Schools',
                'schools-secondary'  => 'Secondary Schools',
            ),
        );
    }
    
    public static function testSyndicationOptions($prefix)
    {
        return array(
            'id'      => $prefix . 'syndication_targets',
            'name'    => 'Testing',
            'type'    => 'checkbox_list',
            // Options of checkboxes, in format 'value' => 'Label'
            'options' => array(
                'wplabs-didi'    => "Diana's lab",
                'wplabs-becks'   => "Becky's lab",
                'wplabs-cubicle' => "Jane's lab",
            ),
        );
    }
    
    public static function secondarySchoolOptions($prefix)
    {
        return array(
            'id'      => $prefix . 'syndication_targets',
            'name'    => 'Secondary Schools',
            'type'    => 'checkbox_list',
            // Options of checkboxes, in format 'value' => 'Label'
            'options' => array(
                'schools-bci' => 'BCI',
                'schools-chc' => 'CHCI',
                'schools-eci' => 'ECI',
                'schools-eds' => 'EDSS',
                'schools-fhc' => 'FHCI',
                'schools-gci' => 'GCI',
                'schools-gps' => 'GPSS',
                'schools-grc' => 'GRCI',
                'schools-hrh' => 'HHSS',
                'schools-jhs' => 'JHSS',
                'schools-kci' => 'KCI',
                'schools-phs' => 'PHS',
                'schools-jam' => 'SJAM',
                'schools-sss' => 'SSS',
                'schools-wci' => 'WCI',
                'schools-wod' => 'WODSS',
            ),
            // Display options in a single row?
            // 'inline' => true,
            // Display "Select All / None" button?
            'select_all_none' => true,
        );
    }
    
    public static function elementarySchoolOptions($prefix)
    {
        return array(
            'id'      => $prefix . 'syndication_targets',
            'name'    => 'Elementary Schools',
            'type'    => 'checkbox_list',
            // Options of checkboxes, in format 'value' => 'Label'
            'options' => array(
                'schools-ark' => 'A R Kaufman',
                'schools-abe' => 'Abraham Erb',
                'schools-alp' => 'Alpine',
                'schools-ave' => 'Avenue Road',
                'schools-ayr' => 'Ayr',
    
                'schools-bdn' => 'Baden',
                'schools-blr' => 'Blair Road',
                'schools-bre' => 'Breslau',
                'schools-brp' => 'Bridgeport',
                'schools-bgd' => 'Brigadoon',
    
                'schools-cdc' => 'Cedar Creek',
                'schools-ced' => 'Cedarbrae',
                'schools-cnc' => 'Centennial (Cambridge)',
                'schools-cnw' => 'Centennial (Waterloo)',
                'schools-ctr' => 'Central',
                'schools-cha' => 'Chalmers Street',
                'schools-chi' => 'Chicopee Hills',
                'schools-cle' => 'Clemens Mill',
                'schools-con' => 'Conestogo',
                'schools-cor' => 'Coronation',
                'schools-coh' => 'Country Hills',
                'schools-crl' => 'Courtland',
                'schools-cre' => 'Crestview',
    
                'schools-doo' => 'Doon',
                'schools-dpk' => 'Driftwood Park',
    
                'schools-est' => 'Edna Staebler',
                'schools-elg' => 'Elgin Street',
                'schools-elz' => 'Elizabeth Ziegler',
                'schools-emp' => 'Empire',
    
                'schools-flo' => 'Floradale',
                'schools-fgl' => 'Forest Glen',
                'schools-fhl' => 'Forest Hill',
                'schools-fra' => 'Franklin',
    
                'schools-gcp' => 'Glencairn',
                'schools-gvc' => 'Grand View (Cambridge)',
                'schools-gvn' => 'Grandview (New Hamburg)',
                'schools-gro' => 'Groh',
    
                'schools-hes' => 'Hespeler',
                'schools-hig' => 'Highland',
                'schools-hil' => 'Hillcrest',
                'schools-how' => 'Howard Robertson',
    
                'schools-jfc' => 'J F Carmichael',
                'schools-jwg' => 'J W Gerth',
                'schools-jme' => 'Janet Metcalfe',
                'schools-jst' => 'Jean Steckle',
                'schools-jdp' => 'John Darling',
                'schools-jma' => 'John Mahood',
    
                'schools-kea' => 'Keatsway',
                'schools-ked' => 'King Edward',
    
                'schools-lkw' => 'Lackner Woods',
                'schools-lrw' => 'Laurelwood',
                'schools-lau' => 'Laurentian',
                'schools-lbp' => 'Lester B Pearson',
                'schools-lex' => 'Lexington',
                'schools-lnh' => 'Lincoln Heights',
                'schools-lin' => 'Linwood',
    
                'schools-mcg' => 'MacGregor',
                'schools-mck' => 'Mackenzie King',
                'schools-man' => 'Manchester',
                'schools-mrg' => 'Margaret Avenue',
                'schools-mjp' => 'Mary Johnston',
                'schools-mea' => 'Meadowlane',
                'schools-mil' => 'Millen Woods',
                'schools-mof' => 'Moffat Creek',
    
                'schools-nam' => 'N A MacEachern',
                'schools-ndd' => 'New Dundee',
                'schools-nlw' => 'Northlake Woods',
    
                'schools-pkm' => 'Park Manor',
                'schools-pkw' => 'Parkway',
                'schools-pio' => 'Pioneer Park',
                'schools-pre' => 'Preston',
                'schools-pru' => 'Prueter',
    
                'schools-qel' => 'Queen Elizabeth',
                'schools-qsm' => 'Queensmount',
    
                'schools-riv' => 'Riverside',
                'schools-roc' => 'Rockway',
                'schools-rmt' => 'Rosemount',
                'schools-rye' => 'Ryerson',
    
                'schools-sag' => 'Saginaw',
                'schools-shl' => 'Sandhills',
                'schools-snd' => 'Sandowne',
                'schools-she' => 'Sheppard',
                'schools-sil' => 'Silverheights',
                'schools-sab' => 'Sir Adam Beck',
                'schools-smi' => 'Smithson',
                'schools-srg' => 'Southridge',
                'schools-sta' => 'St Andrew\'s',
                'schools-stj' => 'St Jacobs',
                'schools-stn' => 'Stanley Park',
                'schools-stw' => 'Stewart Avenue',
                'schools-sud' => 'Suddaby',
                'schools-sun' => 'Sunnyside',
    
                'schools-tai' => 'Tait Street',
                'schools-tri' => 'Trillium',
    
                'schools-vis' => 'Vista Hills',
    
                'schools-wtt' => 'W T Townshend',
                'schools-wel' => 'Wellesley',
                'schools-wsh' => 'Westheights',
                'schools-wsm' => 'Westmount',
                'schools-wsv' => 'Westvale',
                'schools-wgd' => 'William G Davis',
                'schools-wlm' => 'Williamsburg',
                'schools-wls' => 'Wilson Avenue',
                'schools-wcp' => 'Winston Churchill',
                'schools-wpk' => 'Woodland Park',
            ),
            // Display options in a single row?
            // 'inline' => true,
            // Display "Select All / None" button?
            'select_all_none' => true,
        );
    }
}
