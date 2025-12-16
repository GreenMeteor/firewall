# Firewall Core Module for HumHub

## Overview
The **Firewall** core module for HumHub enhances security by providing advanced access control mechanisms. It allows administrators to define rules for restricting or allowing access to the platform based on various conditions.

## Features
- 🔥 **IP Whitelisting & Blacklisting** – Restrict access based on IP addresses using CIDR notation, IP ranges, or wildcards.
- 🔄 **Rate Limiting** – Prevent excessive requests to protect against abuse.
- 🔐 **User & Role-Based Access Rules** – Define granular access policies.
- 📜 **Logging & Monitoring** – Track access attempts and security-related events.
- ⚙️ **Customizable Rules** – Configure rules via the admin panel with priorities and statuses.
- 🛠 **Automatic Cache Clearing** – Ensures firewall rules are always up-to-date after changes.

## Installation
1. Download or clone the repository into your HumHub `/protected/humhub/modules` directory.
2. Run migrations for the module to properly work as it is enabled by default.
3. Configure your firewall rules in the module settings.

## Usage
Once migration are installed, navigate to **Firewall** in the admin panel to set up your access control rules. You can define allowed or blocked IP ranges, enforce rate limits, and customize user-based restrictions.

## Roadmap
- [x] Add support for country-based blocking.
- [ ] Implement email and notifications for blocked attempts.
- [x] Enhance UI for easier rule management.
