# SMS Gateway Delivery Issues - Troubleshooting

## Issue: API Returns Success But SMS Not Delivered

If your error logs show HTTP 200 responses from the SMS Gateway, but you're not receiving SMS messages, here are the steps to troubleshoot:

### 1. Check SMS Gateway App Logs

The SMS Gateway app on your Android device should have its own logs. Check these logs to see:
- If the SMS was actually queued for sending
- Any errors during SMS transmission
- Whether the phone number was recognized correctly

**How to check:**
- Open your SMS Gateway app
- Look for a "Logs" or "History" section
- Check for entries around the time you tried to send

### 2. Verify Phone Number Format

The phone number format is critical. Your SMS Gateway might require:
- **International format**: `639701319849` (with country code, no leading 0)
- **Local format**: `09701319849` (with leading 0)
- **With plus sign**: `+639701319849`

**Current Implementation:**
The code now automatically converts:
- 11-digit numbers starting with 0 → removes 0 and adds 63
- 10-digit numbers → adds 63 country code

**To test different formats:**
1. Check what format your SMS Gateway app expects
2. Modify the phone number cleaning logic in `api/sendSMS.php` if needed

### 3. Check Parameter Names

The log shows the successful API call used `to` and `message` parameters. However, your SMS Gateway might actually need:
- `phone` instead of `to`
- `number` instead of `to`
- `text` instead of `message`

**Current Implementation:**
The code tries multiple parameter combinations. Check the logs to see which one returned 200, then verify if that's the correct format for your SMS Gateway app.

### 4. Verify SMS Permissions

Ensure your SMS Gateway app has:
- ✅ SMS sending permissions
- ✅ Phone permissions (if required)
- ✅ Not blocked by battery optimization
- ✅ Running in background

**Android Settings:**
1. Go to Settings → Apps → [Your SMS Gateway App]
2. Check Permissions → Ensure SMS is allowed
3. Check Battery → Disable battery optimization
4. Check Background → Allow background activity

### 5. Test Directly with SMS Gateway

Test sending an SMS directly through your SMS Gateway app's interface:
1. Open the SMS Gateway app
2. Use the "Send SMS" or "Test" feature
3. Send to the same phone number
4. If this works, the issue is with the API parameters
5. If this doesn't work, the issue is with the app or device

### 6. Check Network and SIM Card

- Ensure your Android device has:
  - Active SIM card with SMS capability
  - Mobile data or WiFi connection
  - Signal strength

### 7. Verify API Response

The response `{"status":"ok", "model":"RMX3195"}` indicates:
- ✅ API connection successful
- ✅ Authentication successful
- ✅ Request accepted

But it doesn't guarantee SMS delivery. Check if your SMS Gateway app provides a more detailed response that includes:
- SMS ID
- Delivery status
- Error messages

### 8. Check SMS Gateway App Documentation

Different SMS Gateway apps use different:
- Parameter names (`phone` vs `to` vs `number`)
- Phone number formats
- Additional required parameters
- Response formats

**Common SMS Gateway Apps:**
- **SMS Gateway (by C.T. Lin)**: Usually uses `phone` and `message`
- **SMS Forwarder**: May use different parameters
- **Custom apps**: Check their specific documentation

### 9. Enable Detailed Logging

The current implementation logs:
- All API attempts
- HTTP codes
- Responses
- Phone numbers used

Check `C:\xampp\apache\logs\error.log` for detailed information.

### 10. Manual Test

You can manually test the SMS Gateway API using a browser or curl:

```bash
# Test with phone parameter
curl "http://192.168.18.28:8080/?phone=639701319849&message=Test" \
  -u sms:admin123

# Test with to parameter  
curl "http://192.168.18.28:8080/?to=639701319849&message=Test" \
  -u sms:admin123

# Test with number parameter
curl "http://192.168.18.28:8080/?number=639701319849&message=Test" \
  -u sms:admin123
```

Compare the responses and check which one actually sends the SMS.

### 11. Check SMS Gateway App Settings

In your SMS Gateway app, check:
- **Default SIM**: If dual SIM, ensure correct SIM is selected
- **SMS Center (SMSC)**: Should be auto-configured, but verify
- **Delivery Reports**: Enable to see if SMS was sent
- **API Settings**: Verify the endpoint and authentication

### Next Steps

1. **Check SMS Gateway app logs** - Most important step
2. **Test with different phone number formats** - Try with/without country code
3. **Test with different parameter names** - The code tries multiple, but verify which one your app actually uses
4. **Test sending directly from the app** - To isolate if it's an API issue or app issue
5. **Check if SMS appears in phone's sent folder** - Sometimes SMS is sent but not delivered

### Common Solutions

1. **Phone number format**: Most SMS Gateway apps need international format without leading 0
2. **Parameter names**: Try `phone` instead of `to` (already in code, but verify which works)
3. **SIM card**: Ensure active SIM with SMS capability
4. **Permissions**: Re-grant SMS permissions to the app
5. **App restart**: Restart the SMS Gateway app after configuration changes

