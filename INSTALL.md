# Installation and Configuration Guide

## Prerequisites

Before installing the Mailing module, ensure you have:

1. **Omeka S** (version 3.0 or higher) installed and running
2. **PHP** 7.4 or higher
3. A **Listmonk instance** running and accessible
4. Listmonk **admin credentials** (username and password)
5. Access to your Omeka S server filesystem (via SSH or FTP)

## Step 1: Install the Module

### Option A: Git Clone (Recommended)

1. SSH into your Omeka S server

2. Navigate to your Omeka S modules directory:
   ```bash
   cd /path/to/omeka-s/modules
   ```

3. Clone the repository:
   ```bash
   git clone https://github.com/samszo/Omeka-S-module-Mailing.git Mailing
   ```

4. Set proper permissions:
   ```bash
   chmod -R 755 Mailing
   chown -R www-data:www-data Mailing  # Adjust user/group as needed
   ```
5. go to the root of the module, and run:

   ```bash
   composer install --no-dev
   ```


### Option B: Manual Download

1. Download the module as a ZIP file from GitHub

2. Extract the ZIP file

3. Rename the extracted folder to `Mailing` (if necessary)

4. Upload the `Mailing` folder to your Omeka S `modules` directory via FTP/SFTP

5. Set proper permissions (see Option A, step 4)

## Step 2: Enable the Module in Omeka S

1. Open your web browser and navigate to your Omeka S admin panel:
   ```
   https://your-omeka-domain.com/admin
   ```

2. Log in with your admin credentials

3. In the left sidebar, click on **Modules**

4. Find **Mailing** in the list of available modules

5. Click the **Install** button next to the Mailing module

6. Wait for the installation to complete (you should see a success message)

## Step 3: Configure Listmonk Connection

### Getting Your Listmonk Credentials

Before configuring the module, you need:

1. **Listmonk URL**: The base URL where your Listmonk instance is running
   - Example: `https://listmonk.example.com`
   - Example: `http://localhost:9000` (for local development)

2. **Admin Username**: Your Listmonk admin username

3. **Admin Password**: Your Listmonk admin password

4. **Default List ID** (optional): The ID of a default mailing list
   - You can find this in Listmonk under Lists → Click a list → Check URL or ID field

### Configure the Module

1. In the Omeka S admin panel, go to **Modules**

2. Find **Mailing** and click the **Configure** button

3. Fill in the configuration form:

   | Field | Description | Example |
   |-------|-------------|---------|
   | **Listmonk URL** | Base URL of your Listmonk installation | `https://listmonk.example.com` |
   | **Listmonk Username** | Your Listmonk admin username | `admin` |
   | **Listmonk Password** | Your Listmonk admin password | `your-secure-password` |
   | **Default List ID** | (Optional) Default list to use | `1` |

4. Click **Submit** to save your configuration

## Step 4: Verify the Connection

1. In the left sidebar of the Omeka S admin panel, click on **Mailing**

2. You should see the Mailing dashboard

3. Check the **Connection Status** section:
   - ✓ **Green message**: "Connected to Listmonk successfully" - Everything is working!
   - ✗ **Red message**: "Unable to connect to Listmonk" - See troubleshooting below

## Step 5: Explore the Module Features

Once connected, you can:

### View Subscribers
1. Click **Mailing** in the sidebar
2. Click **View Subscribers**
3. Browse all subscribers from your Listmonk instance

### View Mailing Lists
1. Click **Mailing** in the sidebar
2. Click **View Lists**
3. See all your mailing lists with statistics

### Monitor Campaigns
1. Click **Mailing** in the sidebar
2. Click **View Campaigns**
3. View campaign statistics (opens, clicks, status)

## Troubleshooting

### Connection Issues

**Problem**: "Unable to connect to Listmonk" error

**Solutions**:

1. **Check Listmonk URL**
   - Ensure the URL is correct and includes the protocol (`http://` or `https://`)
   - Don't include `/api` in the URL - the module adds it automatically
   - Example: Use `https://listmonk.example.com`, not `https://listmonk.example.com/api`

2. **Verify Credentials**
   - Double-check your username and password
   - Try logging into Listmonk web interface with the same credentials
   - Ensure the account has admin privileges

3. **Test Network Connectivity**
   - From your Omeka S server, test if Listmonk is reachable:
     ```bash
     curl https://listmonk.example.com/api/health
     ```
   - Should return: `{"data": "pong"}`

4. **Check Firewall Settings**
   - If Listmonk is on a different server, ensure firewall allows connections
   - Check if Listmonk is configured to accept external connections

5. **Review Listmonk Configuration**
   - Ensure Listmonk API is enabled
   - Check `config.toml` for API settings

6. **HTTPS/SSL Issues**
   - If using self-signed certificates, you may need to configure PHP to accept them
   - Consider using valid SSL certificates (Let's Encrypt is free)

### No Data Showing

**Problem**: Module connects but shows no subscribers/lists/campaigns

**Solutions**:

1. **Check Listmonk has data**
   - Log into Listmonk web interface
   - Verify you have subscribers, lists, or campaigns created

2. **API Permissions**
   - Ensure the Listmonk user has API access
   - Check Listmonk logs for API request errors

### Module Not Appearing

**Problem**: Mailing module doesn't appear in Modules list

**Solutions**:

1. **Check folder name**: Must be exactly `Mailing` (case-sensitive)

2. **Check file structure**: Ensure `Module.php` is at `modules/Mailing/Module.php`

3. **Check permissions**: Module files must be readable by web server

4. **Clear cache**:
   ```bash
   rm -rf /path/to/omeka-s/application/data/cache/*
   ```

### PHP Errors

**Problem**: PHP errors when using the module

**Solutions**:

1. **Check PHP version**: Requires PHP 7.4 or higher
   ```bash
   php -v
   ```

2. **Check error logs**:
   - Omeka S: `files/error_log` or web server error log
   - Look for specific error messages

3. **Enable debug mode** in Omeka S `config/local.config.php`:
   ```php
   return [
       'logger' => [
           'log' => true,
       ],
   ];
   ```

## Updating the Module

To update to a newer version:

1. Backup your Omeka S installation (database and files)

2. Navigate to the module directory:
   ```bash
   cd /path/to/omeka-s/modules/Mailing
   ```

3. Pull the latest changes:
   ```bash
   git pull origin main
   ```

4. Log into Omeka S admin panel

5. No additional steps needed - configuration is preserved

## Uninstalling the Module

To completely remove the module:

1. Log into Omeka S admin panel

2. Go to **Modules**

3. Find **Mailing** and click **Uninstall**

4. Confirm the uninstallation

5. (Optional) Delete the module folder:
   ```bash
   rm -rf /path/to/omeka-s/modules/Mailing
   ```

Note: Uninstalling removes all module settings but does NOT affect your Listmonk data.

## Security Best Practices

1. **Use HTTPS**: Always use HTTPS for both Omeka S and Listmonk in production

2. **Strong Passwords**: Use strong, unique passwords for Listmonk

3. **Restrict Access**: Limit Listmonk API access to trusted IP addresses if possible

4. **Regular Updates**: Keep Omeka S, PHP, and Listmonk updated

5. **Backup Regularly**: Regular backups of both Omeka S and Listmonk data

## Getting Help

If you encounter issues:

1. Check this guide's troubleshooting section
2. Review the [README.md](README.md) file
3. Check the [ARCHITECTURE.md](ARCHITECTURE.md) for technical details
4. Open an issue on [GitHub](https://github.com/samszo/Omeka-S-module-Mailing/issues)

## Additional Resources

- [Omeka S Documentation](https://omeka.org/s/docs/)
- [Listmonk Documentation](https://listmonk.app/docs/)
- [Listmonk API Documentation](https://listmonk.app/docs/apis/apis/)
