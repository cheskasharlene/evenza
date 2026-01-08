# SMS Gateway App Not Sending SMS - Troubleshooting Guide

## Issue: Cannot Send SMS Manually from SMS Gateway App

If you cannot send SMS manually from the SMS Gateway app, the problem is with the app configuration or device setup, not the API integration.

## Critical Tests

### Test 1: Can You Send Regular SMS?
**Most Important Test First**

1. Open your phone's **default messaging app** (not SMS Gateway)
2. Try sending a regular SMS to any number
3. **Result determines next steps:**
   - ✅ **If regular SMS works**: Issue is with SMS Gateway app configuration
   - ❌ **If regular SMS doesn't work**: Issue is with SIM card, network, or carrier

### Test 2: Check SMS Gateway App Permissions

1. **Go to Android Settings**:
   - Settings → Apps → [Your SMS Gateway App Name]

2. **Check Permissions**:
   - Tap "Permissions"
   - Ensure **"SMS"** permission is **Allowed**
   - If it says "Denied" or "Ask every time", change it to **"Allow"**
   - Also check "Phone" permission if available

3. **Check App Info**:
   - Go back to app info
   - Tap "Advanced" or "Additional settings"
   - Check "Modify system settings" - should be allowed
   - Check "Display over other apps" - may need to be allowed

### Test 3: Set as Default SMS App (If Required)

Some Android versions require the SMS Gateway app to be the default SMS app:

1. **Go to Settings**:
   - Settings → Apps → Default apps → SMS app
   - Select your SMS Gateway app
   - Confirm the change

2. **Test Again**:
   - Try sending SMS manually from SMS Gateway app
   - If it works, keep it as default
   - If it doesn't, you can change it back later

### Test 4: Check SIM Card Status

1. **Go to Settings**:
   - Settings → About phone → SIM status
   - Or Settings → Network & Internet → SIM cards

2. **Verify**:
   - SIM card is active
   - Mobile network is connected
   - Signal strength is good
   - Phone number is displayed correctly

3. **Check SIM Card**:
   - Ensure SIM card is properly inserted
   - Try removing and reinserting SIM card
   - Try the SIM card in another phone to verify it works

### Test 5: Check Battery Optimization

1. **Go to Settings**:
   - Settings → Apps → [Your SMS Gateway App] → Battery

2. **Disable Optimization**:
   - Tap "Battery optimization"
   - Find your SMS Gateway app
   - Select "Don't optimize" or "Not optimized"
   - This ensures the app can run in background

### Test 6: Check App Settings

1. **Open SMS Gateway App**:
   - Go to Settings within the app
   - Look for:
     - **SIM Selection**: If dual SIM, ensure correct SIM is selected
     - **SMS Center (SMSC)**: Should be auto-configured, but verify
     - **Default SIM**: Ensure it's set correctly
     - **Permissions**: Check if app shows any permission warnings

2. **Check App Logs**:
   - Look for error messages
   - Check if there are any warnings about permissions or SIM

### Test 7: Check Android Version Compatibility

1. **Check Android Version**:
   - Settings → About phone → Android version

2. **Known Issues**:
   - Android 6.0+ requires runtime permissions
   - Android 8.0+ has stricter background restrictions
   - Android 10+ has scoped storage restrictions
   - Some custom Android versions (MIUI, OneUI) have additional restrictions

### Test 8: Try Different SMS Gateway App

If the current app doesn't work, try an alternative:

**Popular SMS Gateway Apps:**
1. **SMS Gateway (by C.T. Lin)** - Most popular
2. **SMS Forwarder** - Alternative option
3. **Tasker** - Advanced automation (requires setup)

**Steps:**
1. Install alternative SMS Gateway app
2. Configure it with same settings
3. Test manual sending
4. If it works, use that app instead

## Common Solutions

### Solution 1: Grant All Permissions

1. Settings → Apps → [SMS Gateway App] → Permissions
2. Enable ALL permissions:
   - SMS (Send and Receive)
   - Phone (if available)
   - Storage (if available)
   - Contacts (if available)

### Solution 2: Disable Battery Optimization

1. Settings → Apps → [SMS Gateway App] → Battery
2. Set to "Don't optimize"
3. Enable "Allow background activity"

### Solution 3: Set as Default SMS App

1. Settings → Apps → Default apps → SMS app
2. Select your SMS Gateway app
3. Test sending SMS

### Solution 4: Reinstall App

1. Uninstall SMS Gateway app
2. Restart phone
3. Reinstall app
4. Grant all permissions during installation
5. Configure settings
6. Test again

### Solution 5: Check Carrier Restrictions

1. **Contact Your Carrier**:
   - Some carriers block automated SMS
   - Some require special plans for SMS Gateway apps
   - Ask if there are any restrictions

2. **Check SMS Balance**:
   - Ensure you have SMS credits/balance
   - Some carriers limit daily SMS count

### Solution 6: Check Phone Number Format

When testing manually, try different formats:
- `09701319849` (with 0)
- `639701319849` (international)
- `+639701319849` (with +)

See which format works when sending from regular messaging app.

## Step-by-Step Diagnostic Process

1. **First**: Test if regular SMS works from default messaging app
   - If NO → Fix SIM/network/carrier issues first
   - If YES → Continue to step 2

2. **Second**: Check SMS Gateway app permissions
   - Grant all permissions
   - Test again

3. **Third**: Set SMS Gateway as default SMS app
   - Test again

4. **Fourth**: Disable battery optimization
   - Test again

5. **Fifth**: Check app settings (SIM selection, etc.)
   - Test again

6. **Sixth**: Try different SMS Gateway app
   - If works → Use that app
   - If doesn't work → May be device/carrier issue

## If Nothing Works

If after all these steps SMS Gateway app still can't send SMS:

1. **Check Device Compatibility**:
   - Some devices have restrictions
   - Check app's compatibility list
   - Check if device manufacturer has SMS restrictions

2. **Try Different Device**:
   - Test on another Android device
   - If works on other device → Original device has restrictions
   - If doesn't work → App or carrier issue

3. **Contact App Developer**:
   - Report the issue
   - Provide device model, Android version
   - Provide error logs from app

4. **Alternative Solutions**:
   - Use SMS API service (Twilio, etc.) instead of Android app
   - Use email notifications
   - Use push notifications
   - Use WhatsApp Business API

## Quick Checklist

- [ ] Can send regular SMS from default messaging app?
- [ ] SMS Gateway app has SMS permission enabled?
- [ ] SMS Gateway app set as default SMS app?
- [ ] Battery optimization disabled for SMS Gateway app?
- [ ] SIM card is active and has signal?
- [ ] Correct SIM selected (if dual SIM)?
- [ ] Tried different SMS Gateway app?
- [ ] Checked app logs for errors?
- [ ] Contacted carrier about restrictions?

## Next Steps

Once you can send SMS manually from the SMS Gateway app:
1. Note the exact phone number format that works
2. Update the API code to use that format
3. Test automatic sending from the system
4. Everything should work end-to-end

