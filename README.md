# Firewall Core Module for HumHub

> [!IMPORTANT]
> 🚧 **Work in Progress** 🚧

## Overview
The **Firewall** core module for HumHub enhances security by providing advanced access control mechanisms. It allows administrators to define rules for restricting or allowing access to the platform based on various conditions.

## Features
- 🔥 **IP Whitelisting & Blacklisting** – Restrict access based on IP addresses using CIDR notation, IP ranges, or wildcards.
- 🔄 **Rate Limiting** – Prevent excessive requests to protect against abuse.
- 🔐 **User & Role-Based Access Rules** – Define granular access policies.
- 📜 **Logging & Monitoring** – Track access attempts and security-related events.
- ⚙️ **Customizable Rules** – Configure rules via the admin panel with priorities and statuses.
- 🛠 **Automatic Cache Clearing** – Ensures firewall rules are always up-to-date after changes.

## Database Models
### Firewall Rules (`firewall_rule`)
This model defines firewall rules for controlling access based on IP addresses.

#### Attributes:
- **`ip_range`**: Defines allowed or denied IP ranges (CIDR, wildcard, or range-based).
- **`action`**: Either `allow` or `deny`.
- **`priority`**: Determines the order of rule evaluation.
- **`status`**: Active or inactive.
- **`created_by` / `updated_by`**: Tracks the admin responsible for changes.

#### Validation:
- Supports CIDR, wildcard, and IP range formats.
- Ensures valid IP addresses and logical range ordering.

### Firewall Logs (`firewall_log`)
This model logs all access attempts for monitoring purposes.

#### Attributes:
- **`ip`**: The IP address of the request.
- **`url`**: The requested URL.
- **`user_agent`**: The request’s user agent.
- **`created_at`**: Timestamp of the event.

## Installation
1. Download or clone the repository into your HumHub `protected/humhub/modules` directory.
2. Run migrations for the module to properly work as it is enabled by default.
3. Configure your firewall rules in the module settings.

## Usage
Once installed, navigate to the **Firewall** in the admin panel to set up your access control rules. You can define allowed or blocked IP ranges, enforce rate limits, and customize user-based restrictions.

## Roadmap
- [x] Add support for country-based blocking.
- [ ] Implement email and notifications for blocked attempts.
- [x] Enhance UI for easier rule management.
