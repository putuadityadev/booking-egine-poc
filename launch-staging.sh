#!/usr/bin/env bash
# =============================================================================
# launch-staging.sh — Booking Engine POC Staging Launcher
# Assumes backend already running via Docker on BACKEND_PORT
# =============================================================================
# Usage: ./launch-staging.sh [backend_port]
# Default backend port: 8002
# =============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
FRONTEND_DIR="$SCRIPT_DIR/frontend"
BACKEND_PORT="${1:-8002}"
FRONTEND_PORT=4173
TUNNEL_BE_LOG="/tmp/cf-tunnel-be-$$.log"
TUNNEL_FE_LOG="/tmp/cf-tunnel-fe-$$.log"
TUNNEL_BE_PID=""
TUNNEL_FE_PID=""
PREVIEW_PID=""

GREEN='\033[0;32m'; YELLOW='\033[1;33m'; CYAN='\033[0;36m'
RED='\033[0;31m'; BOLD='\033[1m'; RESET='\033[0m'

echo ""
echo -e "${BOLD}${CYAN}╔══════════════════════════════════════════════╗${RESET}"
echo -e "${BOLD}${CYAN}║   Booking Engine POC — Staging Launcher      ║${RESET}"
echo -e "${BOLD}${CYAN}╚══════════════════════════════════════════════╝${RESET}"
echo ""

# ── Cleanup on exit ───────────────────────────────────────────────────────────
cleanup() {
    echo ""
    echo -e "${YELLOW}🛑 Stopping services...${RESET}"
    [ -n "$PREVIEW_PID"   ] && kill $PREVIEW_PID   2>/dev/null && echo "   ✓ Frontend preview stopped"
    [ -n "$TUNNEL_FE_PID" ] && kill $TUNNEL_FE_PID 2>/dev/null && echo "   ✓ Frontend tunnel stopped"
    [ -n "$TUNNEL_BE_PID" ] && kill $TUNNEL_BE_PID 2>/dev/null && echo "   ✓ Backend tunnel stopped"
    rm -f "$TUNNEL_BE_LOG" "$TUNNEL_FE_LOG"
    echo -e "${GREEN}Done.${RESET}"; echo ""
}
trap cleanup EXIT INT TERM

# Helper: wait for CF tunnel URL from log file
wait_for_tunnel_url() {
    local log_file="$1"
    local url=""
    for i in $(seq 1 30); do
        url=$(grep -oP 'https://[a-z0-9\-]+\.trycloudflare\.com' "$log_file" 2>/dev/null | head -1)
        if [ -n "$url" ]; then
            echo "$url"
            return 0
        fi
        sleep 1
        echo -n "." >&2
    done
    return 1
}

# ── STEP 1: Check Docker backend is alive ────────────────────────────────────
echo -e "${BOLD}[1/4] Checking backend on port $BACKEND_PORT...${RESET}"
STATUS=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:$BACKEND_PORT/api/status" 2>/dev/null || echo "000")
if [ "$STATUS" != "200" ]; then
    echo -e "${RED}✗ Backend not responding on port $BACKEND_PORT (HTTP $STATUS).${RESET}"
    echo -e "${YELLOW}  Make sure Docker is running: docker-compose up -d${RESET}"
    exit 1
fi
echo -e "${GREEN}   ✓ Backend alive on :$BACKEND_PORT${RESET}"; echo ""

# ── STEP 2: Start backend CF Tunnel ──────────────────────────────────────────
echo -e "${BOLD}[2/4] Tunneling backend...${RESET}"
cloudflared tunnel --url "http://localhost:$BACKEND_PORT" > "$TUNNEL_BE_LOG" 2>&1 &
TUNNEL_BE_PID=$!

echo -n "   Waiting"
TUNNEL_BE_URL=$(wait_for_tunnel_url "$TUNNEL_BE_LOG") || {
    echo -e "\n${RED}✗ Could not get backend tunnel URL.${RESET}"; cat "$TUNNEL_BE_LOG"; exit 1
}
echo ""
echo -e "${GREEN}   ✓ Backend tunnel: ${BOLD}$TUNNEL_BE_URL${RESET}"; echo ""

# ── STEP 3: Build frontend with backend tunnel URL ────────────────────────────
echo -e "${BOLD}[3/4] Building frontend...${RESET}"
cd "$FRONTEND_DIR"

[ ! -d "node_modules" ] && echo "   Installing npm deps..." && npm install -q

VITE_BFF_API_URL="$TUNNEL_BE_URL" npm run build 2>&1 | tail -3
echo -e "${GREEN}   ✓ Frontend built${RESET}"

# Start frontend preview server (serves built dist/)
npx --yes vite preview --host 0.0.0.0 --port $FRONTEND_PORT > /tmp/fe-preview-$$.log 2>&1 &
PREVIEW_PID=$!
sleep 2
echo -e "${GREEN}   ✓ Preview server started on :$FRONTEND_PORT${RESET}"; echo ""

# ── STEP 4: Tunnel frontend ───────────────────────────────────────────────────
echo -e "${BOLD}[4/4] Tunneling frontend...${RESET}"
cloudflared tunnel --url "http://localhost:$FRONTEND_PORT" > "$TUNNEL_FE_LOG" 2>&1 &
TUNNEL_FE_PID=$!

echo -n "   Waiting"
TUNNEL_FE_URL=$(wait_for_tunnel_url "$TUNNEL_FE_LOG") || {
    echo -e "\n${RED}✗ Could not get frontend tunnel URL.${RESET}"; cat "$TUNNEL_FE_LOG"; exit 1
}
echo ""
echo -e "${GREEN}   ✓ Frontend tunnel: ${BOLD}$TUNNEL_FE_URL${RESET}"; echo ""

# ── FINAL OUTPUT ──────────────────────────────────────────────────────────────
BOOKING_HOST="${TUNNEL_BE_URL}/api/public/membership/"

echo -e "${BOLD}${GREEN}╔══════════════════════════════════════════════════════════════╗${RESET}"
echo -e "${BOLD}${GREEN}║  🚀 STAGING IS LIVE!                                         ║${RESET}"
echo -e "${BOLD}${GREEN}╠══════════════════════════════════════════════════════════════╣${RESET}"
echo -e "${BOLD}${GREEN}║                                                              ║${RESET}"
echo -e "${BOLD}${GREEN}║  🔗 SHARE THIS LINK TO QA:                                   ║${RESET}"
printf "${BOLD}${CYAN}║  ➜  %-57s║${RESET}\n" "$TUNNEL_FE_URL"
echo -e "${BOLD}${GREEN}║                                                              ║${RESET}"
echo -e "${BOLD}${GREEN}╠══════════════════════════════════════════════════════════════╣${RESET}"
printf "${BOLD}${GREEN}║  📡 Backend API : %-43s║${RESET}\n" "$TUNNEL_BE_URL/api"
echo -e "${BOLD}${GREEN}╠══════════════════════════════════════════════════════════════╣${RESET}"
echo -e "${BOLD}${GREEN}║  📋 Paste ke membership-api .env.staging:                    ║${RESET}"
printf "${BOLD}${CYAN}║  BOOKING_ENGINE_HOST=%-41s║${RESET}\n" "$BOOKING_HOST"
echo -e "${BOLD}${GREEN}╚══════════════════════════════════════════════════════════════╝${RESET}"
echo ""
echo -e "   ${YELLOW}Ctrl+C to stop all services${RESET}"
echo ""

# Keep alive
wait $TUNNEL_FE_PID
