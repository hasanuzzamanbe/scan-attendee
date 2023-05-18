<?php

namespace scanAttendee\Classes;

use FluentCrm\App\Models\CustomContactField;
use FluentCrm\App\Models\Lists;
use FluentCrm\App\Models\Subscriber;
use FluentCrm\App\Models\Tag;
use FluentCrm\Framework\Support\Arr;

if (!defined('ABSPATH')) {
    exit;
}

class CRM
{
    public function addContact($data)
    {
        $fullName = isset($data['name']) ? $data['name'] : '';

        if ($fullName) {
            $nameArray = explode(' ', $fullName);
            if (count($nameArray) > 1) {
                $contact['last_name'] = array_pop($nameArray);
                $contact['first_name'] = implode(' ', $nameArray);
            } else {
                $contact['first_name'] = $fullName;
            }
        }

        $contact['email'] = isset($data['email']) ? $data['email'] : '';
        $contact['attendee_id'] = isset($data['attendee_id']) ? $data['attendee_id'] : '';

        $subscriber = Subscriber::where('email', $contact['email'])->first();

        if ($subscriber && isset($data['skip_if_exists']) && $data['skip_if_exists'] == 'yes') {
            return false;
        }

        if ($subscriber) {
            if ($subscriber->ip && isset($contact['ip'])) {
                unset($contact['ip']);
            }
        }

        $user = get_user_by('email', $contact['email']);

        if ($user) {
            $contact['user_id'] = $user->ID;
        }

        if (!$subscriber) {
            if (empty($contact['source'])) {
                $contact['source'] = 'wordcamp_sylhet_booth';
            }

            $contact['status'] = 'subscribed';

            $subscriber = FluentCrmApi('contacts')->createOrUpdate($contact, false, false);
            if ($subscriber->status == 'pending') {
                $subscriber->sendDoubleOptinEmail();
            }
        } else {
            $contact['status'] = 'subscribed';
            $subscriber = FluentCrmApi('contacts')->createOrUpdate($contact, true, false);
        }
    }
}
