#!/bin/bash

# ============================================
# CONFIGURE IP SCRIPT - WARUNG KAFE PROJECT
# ============================================
# Script untuk mengkonfigurasi IP server dan client
# Usage: ./configure.sh [server_ip] [client_ip]

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_header() {
    echo -e "${BLUE}============================================${NC}"
    echo -e "${BLUE}    WARUNG KAFE IP CONFIGURATION v1.0    ${NC}"
    echo -e "${BLUE}============================================${NC}"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Function to validate IP format
validate_ip() {
    local ip=$1
    if [[ $ip =~ ^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$ ]]; then
        IFS='.' read -ra ADDR <<< "$ip"
        for i in "${ADDR[@]}"; do
            if [[ $i -gt 255 ]]; then
                return 1
            fi
        done
        return 0
    else
        return 1
    fi
}

# Function to get current IP
get_current_ip() {
    # Try to get IP from various interfaces
    local ip=$(hostname -I 2>/dev/null | awk '{print $1}')
    if [[ -z $ip ]]; then
        ip=$(ip route get 1.1.1.1 2>/dev/null | awk '{print $7}' | head -1)
    fi
    if [[ -z $ip ]]; then
        ip=$(ifconfig 2>/dev/null | grep -E "inet.*broadcast" | awk '{print $2}' | head -1)
    fi
    echo $ip
}

# Function to create backup
create_backup() {
    local file=$1
    if [[ -f $file ]]; then
        cp "$file" "$file.backup.$(date +%Y%m%d_%H%M%S)"
        print_success "Backup created: $file.backup.$(date +%Y%m%d_%H%M%S)"
    fi
}

# Function to update client config
update_client_config() {
    local server_ip=$1

    print_info "Updating client configuration..."

    # Update config.js
    local config_file="tugas_kafe_client.1.0/config.js"
    if [[ -f $config_file ]]; then
        create_backup "$config_file"
        sed -i "s|SERVER_URL: \"[^\"]*\"|SERVER_URL: \"http://$server_ip/tugas_kafe_server\"|g" "$config_file"
        print_success "Updated $config_file"
    else
        print_error "File $config_file not found!"
        return 1
    fi
}

# Function to update server config
update_server_config() {
    local server_ip=$1
    local client_ip=$2

    print_info "Updating server configuration..."

    # Update CORS and image URLs
    local files=(
        "tugas_kafe_server.1.0/config/database.php"
        "tugas_kafe_server.1.0/api/menus/read.php"
        "tugas_kafe_server.1.0/api/menus/create.php"
        "tugas_kafe_server.1.0/api/menus/update.php"
    )

    for file in "${files[@]}"; do
        if [[ -f $file ]]; then
            create_backup "$file"

            # Update localhost to server_ip in image URLs
            if [[ $file == *"menus"* ]]; then
                sed -i "s|http://localhost/tugas_kafe_server|http://$server_ip/tugas_kafe_server|g" "$file"
                print_success "Updated image URLs in $file"
            fi

            # Update CORS settings in database.php
            if [[ $file == *"database.php"* ]]; then
                # Add client IP to allowed origins if not exists
                if ! grep -q "http://$client_ip" "$file"; then
                    sed -i "/\"http:\/\/127\.0\.0\.1\",/a\\    \"http://$client_ip\",\n    \"http://$client_ip/tugas_kafe_client\"," "$file"
                    print_success "Added $client_ip to CORS origins in $file"
                fi
            fi
        else
            print_error "File $file not found!"
            return 1
        fi
    done
}

# Function to show current configuration
show_current_config() {
    print_info "Current Configuration:"
    echo ""

    # Show client config
    if [[ -f "tugas_kafe_client.1.0/config.js" ]]; then
        echo "📱 Client Configuration:"
        grep "SERVER_URL" "tugas_kafe_client.1.0/config.js" | sed 's/^/   /'
    fi

    echo ""

    # Show server CORS config
    if [[ -f "tugas_kafe_server.1.0/config/database.php" ]]; then
        echo "🖥️  Server CORS Configuration:"
        grep -A 10 "allowed_origins" "tugas_kafe_server.1.0/config/database.php" | sed 's/^/   /'
    fi

    echo ""
}

# Function to test connection
test_connection() {
    local server_ip=$1
    print_info "Testing connection to server..."

    # Test if server is reachable
    if ping -c 1 "$server_ip" >/dev/null 2>&1; then
        print_success "Server $server_ip is reachable"
    else
        print_warning "Server $server_ip is not reachable (ping failed)"
    fi

    # Test API endpoint if curl is available
    if command -v curl >/dev/null 2>&1; then
        if curl -s "http://$server_ip/tugas_kafe_server/api/menus/read.php" >/dev/null; then
            print_success "API endpoint is accessible"
        else
            print_warning "API endpoint might not be accessible"
        fi
    fi
}

# Function to show help
show_help() {
    echo "Usage: $0 [SERVER_IP] [CLIENT_IP]"
    echo ""
    echo "Options:"
    echo "  -h, --help     Show this help message"
    echo "  -s, --show     Show current configuration"
    echo "  -t, --test     Test connection to server"
    echo "  -i, --interactive  Interactive mode"
    echo ""
    echo "Examples:"
    echo "  $0 192.168.1.100 192.168.1.50"
    echo "  $0 --show"
    echo "  $0 --test"
    echo "  $0 --interactive"
}

# Interactive mode
interactive_mode() {
    print_info "Starting interactive mode..."
    echo ""

    # Get current IP as default
    current_ip=$(get_current_ip)

    # Ask for server IP
    while true; do
        read -p "Enter Server IP [$current_ip]: " server_ip
        server_ip=${server_ip:-$current_ip}

        if validate_ip "$server_ip"; then
            break
        else
            print_error "Invalid IP format! Please try again."
        fi
    done

    # Ask for client IP
    while true; do
        read -p "Enter Client IP [$current_ip]: " client_ip
        client_ip=${client_ip:-$current_ip}

        if validate_ip "$client_ip"; then
            break
        else
            print_error "Invalid IP format! Please try again."
        fi
    done

    echo ""
    print_info "Configuration Summary:"
    echo "   Server IP: $server_ip"
    echo "   Client IP: $client_ip"
    echo ""

    read -p "Do you want to continue? (y/N): " confirm
    if [[ $confirm =~ ^[Yy]$ ]]; then
        update_client_config "$server_ip"
        update_server_config "$server_ip" "$client_ip"
        test_connection "$server_ip"

        echo ""
        print_success "Configuration completed successfully!"
        echo ""
        show_current_config
    else
        print_info "Configuration cancelled."
    fi
}

# Main execution
main() {
    print_header
    echo ""

    # Check if in correct directory
    if [[ ! -d "tugas_kafe_client.1.0" || ! -d "tugas_kafe_server.1.0" ]]; then
        print_error "This script must be run from the project root directory!"
        print_error "Please run from directory containing tugas_kafe_client.1.0 and tugas_kafe_server.1.0"
        exit 1
    fi

    # Parse arguments
    case $1 in
        -h|--help)
            show_help
            exit 0
            ;;
        -s|--show)
            show_current_config
            exit 0
            ;;
        -t|--test)
            if [[ -n $2 ]]; then
                test_connection "$2"
            else
                print_error "Please provide server IP for testing"
                echo "Usage: $0 --test SERVER_IP"
            fi
            exit 0
            ;;
        -i|--interactive)
            interactive_mode
            exit 0
            ;;
        "")
            # No arguments - go to interactive mode
            interactive_mode
            ;;
        *)
            # Two IP arguments provided
            server_ip=$1
            client_ip=$2

            if ! validate_ip "$server_ip"; then
                print_error "Invalid server IP format!"
                exit 1
            fi

            if ! validate_ip "$client_ip"; then
                print_error "Invalid client IP format!"
                exit 1
            fi

            print_info "Using Server IP: $server_ip"
            print_info "Using Client IP: $client_ip"
            echo ""

            update_client_config "$server_ip"
            update_server_config "$server_ip" "$client_ip"
            test_connection "$server_ip"

            echo ""
            print_success "Configuration completed successfully!"
            echo ""
            show_current_config
            exit 0
            ;;
    esac
}

# Run main function
main "$@"