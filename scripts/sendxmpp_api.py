#!/usr/bin/env python3
import subprocess
import sys


import subprocess

def send_xmpp_message(recipient, message):
    try:
        # CHANGED: Replaced subprocess.Popen with subprocess.run
        # CHANGED: Added input=message, and stderr=subprocess.PIPE
        result = subprocess.run(["sendxmpp", "-d", "-t", recipient], input=message, capture_output=True, text=True)

        if result.returncode != 0:
            print(f"Error: {result.stderr.strip()}")
            return False
        else:
            return True
    except FileNotFoundError:
        print("Error: sendxmpp not found. Please install sendxmpp.")
        return False


if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: python sendxmpp_api.py <recipient> <message>")
        sys.exit(1)

    recipient, message = sys.argv[1:]
    send_xmpp_message(recipient, message)
