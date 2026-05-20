<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function register(Request $request)
    {
        $age = $request->input('age');
        $membershipType = $request->input('membership_type');
        $accessDay = $request->input('access_day');
        $membershipDuration = $request->input('membership_duration');

        $result = $this->calculate_membership_fee($age, $membershipType, $accessDay, $membershipDuration);

        if (str_starts_with($result, 'Error:')) {
            return response()->json([
                'message' => $result
            ], 400);
        }

        return response()->json([
            'message' => $result
        ], 200);
    }

    public function calculate_membership_fee($age, $membership_type, $access_day, $membership_duration)
    {
        if ($age === null || !is_numeric($age) || (int)$age < 12) {
            return 'Error: Age below 12 is not allowed.';
        }
        $age = (int)$age;

        if ($membership_type === null) {
            return 'Error: Invalid membership type.';
        }
        $membership_type = strtolower($membership_type);
        if (!in_array($membership_type, ['basic', 'regular', 'student', 'premium'])) {
            return 'Error: Invalid membership type.';
        }

        if ($access_day === null) {
            return 'Error: Invalid access day value.';
        }
        $access_day = strtolower($access_day);
        if (!in_array($access_day, ['weekday', 'weekend'])) {
            return 'Error: Invalid access day value.';
        }

        if ($membership_duration === null || !is_numeric($membership_duration)) {
            return 'Error: Duration must be between 1–12 months.';
        }
        $membership_duration = (int)$membership_duration;
        if ($membership_duration < 1 || $membership_duration > 12) {
            return 'Error: Duration must be between 1–12 months.';
        }

        if ($age === 12) {
            return 'Registration successful. Minimum age accepted.';
        }

        if ($membership_type === 'premium') {
            if ($age >= 60 && $access_day === 'weekend') {
                return 'Registration successful. Unlimited access. 30% discount + $5 fee.';
            }
            return 'Registration successful. Unlimited access granted.';
        }

        if ($age >= 60) {
            return 'Registration successful. 30% senior discount applied.';
        }

        if ($membership_type === 'student') {
            if ($access_day === 'weekend') {
                return 'Registration successful. 20% discount and $5 weekend fee applied.';
            }
            return 'Registration successful. 20% student discount applied.';
        }

        if ($membership_duration === 1) {
            return 'Registration successful. Minimum duration accepted.';
        }

        if ($membership_duration === 12) {
            return 'Registration successful. Maximum duration accepted.';
        }

        if ($access_day === 'weekend') {
            return 'Registration successful. Standard price + $5 weekend fee.';
        }

        return 'Registration successful. Standard price applied.';
    }
}
