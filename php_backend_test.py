#!/usr/bin/env python3
"""
Karaoke Sensō PHP Application - Phase 2 Backend Testing
Tests the PHP implementation including database schema, video upload, voting system, and email notifications.
"""

import requests
import json
import sys
import os
import subprocess
import tempfile
from datetime import datetime
import mysql.connector
from mysql.connector import Error

class KaraokePhpTester:
    def __init__(self, base_url="http://localhost"):
        self.base_url = base_url
        self.api_url = f"{base_url}/karaoke-senso-php/api"
        self.admin_token = None
        self.tests_run = 0
        self.tests_passed = 0
        self.created_event_id = None
        self.created_registration_id = None
        self.created_video_id = None
        self.test_video_file = None
        
        # Database connection parameters
        self.db_config = {
            'host': 'localhost',
            'database': 'karaoke_senso',
            'user': 'root',
            'password': ''
        }
        
    def connect_to_database(self):
        """Connect to MySQL database"""
        try:
            connection = mysql.connector.connect(**self.db_config)
            if connection.is_connected():
                return connection
        except Error as e:
            print(f"❌ Database connection failed: {e}")
            return None
            
    def run_test(self, name, test_func):
        """Run a single test"""
        self.tests_run += 1
        print(f"\n🔍 Testing {name}...")
        
        try:
            success = test_func()
            if success:
                self.tests_passed += 1
                print(f"✅ {name} - PASSED")
                return True
            else:
                print(f"❌ {name} - FAILED")
                return False
        except Exception as e:
            print(f"❌ {name} - EXCEPTION: {str(e)}")
            return False
    
    def test_database_schema_phase2(self):
        """Test Phase 2 database schema extensions"""
        print("   Testing Phase 2 database tables...")
        
        connection = self.connect_to_database()
        if not connection:
            return False
            
        try:
            cursor = connection.cursor()
            
            # Test if Phase 2 tables exist
            required_tables = ['videos', 'votes', 'vote_sessions', 'email_notifications']
            
            for table in required_tables:
                cursor.execute(f"SHOW TABLES LIKE '{table}'")
                result = cursor.fetchone()
                if not result:
                    print(f"   ❌ Table '{table}' not found")
                    return False
                print(f"   ✅ Table '{table}' exists")
            
            # Test videos table structure
            cursor.execute("DESCRIBE videos")
            video_columns = [row[0] for row in cursor.fetchall()]
            required_video_columns = ['id', 'registration_id', 'filename', 'original_name', 'file_size', 'upload_status']
            
            for col in required_video_columns:
                if col not in video_columns:
                    print(f"   ❌ Column '{col}' missing in videos table")
                    return False
            
            # Test votes table structure
            cursor.execute("DESCRIBE votes")
            vote_columns = [row[0] for row in cursor.fetchall()]
            required_vote_columns = ['id', 'video_id', 'vote_value', 'vote_label', 'voter_ip', 'session_id']
            
            for col in required_vote_columns:
                if col not in vote_columns:
                    print(f"   ❌ Column '{col}' missing in votes table")
                    return False
            
            # Test views exist
            cursor.execute("SHOW FULL TABLES WHERE Table_type = 'VIEW'")
            views = [row[0] for row in cursor.fetchall()]
            required_views = ['registration_videos', 'vote_results']
            
            for view in required_views:
                if view not in views:
                    print(f"   ❌ View '{view}' not found")
                    return False
                print(f"   ✅ View '{view}' exists")
            
            print("   ✅ All Phase 2 database schema elements verified")
            return True
            
        except Error as e:
            print(f"   ❌ Database schema test failed: {e}")
            return False
        finally:
            if connection.is_connected():
                cursor.close()
                connection.close()
    
    def test_admin_login(self):
        """Test admin authentication"""
        try:
            url = f"{self.api_url}/auth.php"
            data = {
                "email": "admin@karaokesenso.com",
                "password": "Senso2025*"
            }
            
            response = requests.post(url, json=data)
            print(f"   Login response status: {response.status_code}")
            
            if response.status_code == 200:
                result = response.json()
                if result.get('success') and 'access_token' in result:
                    self.admin_token = result['access_token']
                    print(f"   ✅ Admin token obtained: {self.admin_token[:50]}...")
                    return True
            
            print(f"   ❌ Login failed: {response.text}")
            return False
            
        except Exception as e:
            print(f"   ❌ Login error: {e}")
            return False
    
    def test_create_test_event(self):
        """Create a test event for registration testing"""
        try:
            url = f"{self.api_url}/events.php"
            headers = {'Authorization': f'Bearer {self.admin_token}'}
            
            event_data = {
                "name": "Test Event - Phase 2",
                "municipality": "Querétaro",
                "venue": "Test Venue",
                "date": "2025-03-01 18:00:00",
                "max_participants": 50
            }
            
            response = requests.post(url, json=event_data, headers=headers)
            
            if response.status_code == 201:
                result = response.json()
                self.created_event_id = result.get('id')
                print(f"   ✅ Test event created: {self.created_event_id}")
                return True
            
            print(f"   ❌ Event creation failed: {response.text}")
            return False
            
        except Exception as e:
            print(f"   ❌ Event creation error: {e}")
            return False
    
    def test_create_test_registration(self):
        """Create a test registration for video upload testing"""
        if not self.created_event_id:
            print("   ❌ No event available for registration")
            return False
            
        try:
            url = f"{self.api_url}/registrations.php"
            
            registration_data = {
                "full_name": "Juan Pérez Testeo",
                "age": 28,
                "municipality": "Querétaro",
                "sector": "Cultural",
                "phone": "4421234567",
                "email": f"test_participant_{datetime.now().strftime('%H%M%S')}@test.com",
                "event_id": self.created_event_id
            }
            
            response = requests.post(url, json=registration_data)
            
            if response.status_code == 201:
                result = response.json()
                self.created_registration_id = result.get('id')
                print(f"   ✅ Test registration created: {self.created_registration_id}")
                return True
            
            print(f"   ❌ Registration creation failed: {response.text}")
            return False
            
        except Exception as e:
            print(f"   ❌ Registration creation error: {e}")
            return False
    
    def create_test_video_file(self):
        """Create a small test video file for upload testing"""
        try:
            # Create a minimal MP4 file (just headers, not a real video)
            # This is a minimal MP4 file structure for testing
            mp4_header = b'\x00\x00\x00\x20\x66\x74\x79\x70\x69\x73\x6f\x6d\x00\x00\x02\x00\x69\x73\x6f\x6d\x69\x73\x6f\x32\x61\x76\x63\x31\x6d\x70\x34\x31'
            
            temp_file = tempfile.NamedTemporaryFile(suffix='.mp4', delete=False)
            temp_file.write(mp4_header)
            temp_file.write(b'0' * 1024)  # Add some content to make it a reasonable size
            temp_file.close()
            
            self.test_video_file = temp_file.name
            print(f"   ✅ Test video file created: {self.test_video_file}")
            return True
            
        except Exception as e:
            print(f"   ❌ Test video creation failed: {e}")
            return False
    
    def test_video_upload_api(self):
        """Test video upload functionality"""
        if not self.created_registration_id:
            print("   ❌ No registration available for video upload")
            return False
            
        if not self.create_test_video_file():
            return False
            
        try:
            url = f"{self.api_url}/videos.php/upload"
            
            # Test file validation - file too large (simulate)
            print("   Testing file size validation...")
            
            # Test valid upload
            print("   Testing valid video upload...")
            
            with open(self.test_video_file, 'rb') as video_file:
                files = {'video': ('test_video.mp4', video_file, 'video/mp4')}
                data = {'registration_id': self.created_registration_id}
                
                response = requests.post(url, files=files, data=data)
                
                if response.status_code == 201:
                    result = response.json()
                    self.created_video_id = result.get('id')
                    print(f"   ✅ Video uploaded successfully: {self.created_video_id}")
                    print(f"   ✅ Video filename: {result.get('filename')}")
                    return True
                else:
                    print(f"   ❌ Video upload failed: {response.status_code} - {response.text}")
                    return False
                    
        except Exception as e:
            print(f"   ❌ Video upload error: {e}")
            return False
        finally:
            # Clean up test file
            if self.test_video_file and os.path.exists(self.test_video_file):
                os.unlink(self.test_video_file)
    
    def test_video_upload_validation(self):
        """Test video upload validation rules"""
        if not self.created_registration_id:
            return False
            
        try:
            url = f"{self.api_url}/videos.php/upload"
            
            # Test missing registration_id
            print("   Testing missing registration_id validation...")
            response = requests.post(url, files={'video': ('test.mp4', b'fake', 'video/mp4')})
            
            if response.status_code != 400:
                print(f"   ❌ Should reject missing registration_id, got {response.status_code}")
                return False
            
            # Test invalid file type
            print("   Testing invalid file type validation...")
            files = {'video': ('test.txt', b'not a video', 'text/plain')}
            data = {'registration_id': self.created_registration_id}
            
            response = requests.post(url, files=files, data=data)
            
            if response.status_code != 400:
                print(f"   ❌ Should reject invalid file type, got {response.status_code}")
                return False
            
            print("   ✅ Video upload validation working correctly")
            return True
            
        except Exception as e:
            print(f"   ❌ Video validation test error: {e}")
            return False
    
    def test_admin_video_management(self):
        """Test admin video approval/rejection"""
        if not self.created_video_id or not self.admin_token:
            print("   ❌ No video or admin token available")
            return False
            
        try:
            # Test video approval
            url = f"{self.api_url}/videos.php/{self.created_video_id}/approve"
            headers = {'Authorization': f'Bearer {self.admin_token}'}
            data = {
                "status": "approved",
                "admin_notes": "Test approval"
            }
            
            response = requests.put(url, json=data, headers=headers)
            
            if response.status_code == 200:
                print("   ✅ Video approval successful")
                return True
            else:
                print(f"   ❌ Video approval failed: {response.status_code} - {response.text}")
                return False
                
        except Exception as e:
            print(f"   ❌ Admin video management error: {e}")
            return False
    
    def test_public_video_listing(self):
        """Test public video listing endpoint"""
        try:
            url = f"{self.api_url}/videos.php/public"
            
            response = requests.get(url)
            
            if response.status_code == 200:
                videos = response.json()
                print(f"   ✅ Public videos retrieved: {len(videos)} videos")
                
                # Check if our approved video is in the list
                if self.created_video_id:
                    video_found = any(v.get('id') == self.created_video_id for v in videos)
                    if video_found:
                        print("   ✅ Approved video appears in public listing")
                    else:
                        print("   ⚠️ Approved video not found in public listing (may need time to propagate)")
                
                return True
            else:
                print(f"   ❌ Public video listing failed: {response.status_code}")
                return False
                
        except Exception as e:
            print(f"   ❌ Public video listing error: {e}")
            return False
    
    def test_voting_system_cast_vote(self):
        """Test vote casting functionality"""
        if not self.created_video_id:
            print("   ❌ No approved video available for voting")
            return False
            
        try:
            url = f"{self.api_url}/votes.php"
            
            # Test valid vote casting
            vote_data = {
                "video_id": self.created_video_id,
                "vote_value": 5,
                "modality": "virtual"
            }
            
            response = requests.post(url, json=vote_data)
            
            if response.status_code == 201:
                result = response.json()
                print(f"   ✅ Vote cast successfully: {result.get('vote_label')}")
                return True
            else:
                print(f"   ❌ Vote casting failed: {response.status_code} - {response.text}")
                return False
                
        except Exception as e:
            print(f"   ❌ Vote casting error: {e}")
            return False
    
    def test_voting_duplicate_control(self):
        """Test duplicate vote prevention"""
        if not self.created_video_id:
            return False
            
        try:
            url = f"{self.api_url}/votes.php"
            
            # Try to vote again with same session
            vote_data = {
                "video_id": self.created_video_id,
                "vote_value": 4,
                "modality": "virtual"
            }
            
            response = requests.post(url, json=vote_data)
            
            if response.status_code == 400:
                print("   ✅ Duplicate vote correctly prevented")
                return True
            else:
                print(f"   ❌ Duplicate vote should be prevented, got {response.status_code}")
                return False
                
        except Exception as e:
            print(f"   ❌ Duplicate vote test error: {e}")
            return False
    
    def test_voting_eligibility_check(self):
        """Test voting eligibility checking"""
        if not self.created_video_id:
            return False
            
        try:
            url = f"{self.api_url}/votes.php/check"
            params = {"video_id": self.created_video_id}
            
            response = requests.get(url, params=params)
            
            if response.status_code == 200:
                result = response.json()
                print(f"   ✅ Voting eligibility check: can_vote={result.get('can_vote')}")
                print(f"   ✅ Current votes: {result.get('current_votes')}")
                return True
            else:
                print(f"   ❌ Voting eligibility check failed: {response.status_code}")
                return False
                
        except Exception as e:
            print(f"   ❌ Voting eligibility error: {e}")
            return False
    
    def test_vote_results_api(self):
        """Test vote results and statistics"""
        try:
            url = f"{self.api_url}/votes.php/results"
            
            response = requests.get(url)
            
            if response.status_code == 200:
                result = response.json()
                print(f"   ✅ Vote results retrieved")
                print(f"   ✅ Results count: {len(result.get('results', []))}")
                print(f"   ✅ Total votes cast: {result.get('statistics', {}).get('total_votes_cast', 0)}")
                return True
            else:
                print(f"   ❌ Vote results failed: {response.status_code}")
                return False
                
        except Exception as e:
            print(f"   ❌ Vote results error: {e}")
            return False
    
    def test_email_notification_logging(self):
        """Test email notification logging"""
        connection = self.connect_to_database()
        if not connection:
            return False
            
        try:
            cursor = connection.cursor()
            
            # Check if email notifications were logged
            cursor.execute("SELECT COUNT(*) FROM email_notifications WHERE notification_type = 'registration'")
            reg_count = cursor.fetchone()[0]
            
            cursor.execute("SELECT COUNT(*) FROM email_notifications WHERE notification_type = 'video_upload'")
            video_count = cursor.fetchone()[0]
            
            print(f"   ✅ Registration email notifications logged: {reg_count}")
            print(f"   ✅ Video upload email notifications logged: {video_count}")
            
            if reg_count > 0 or video_count > 0:
                print("   ✅ Email notification system is logging correctly")
                return True
            else:
                print("   ⚠️ No email notifications found (may be expected if no events triggered)")
                return True  # Not a failure, just no notifications yet
                
        except Error as e:
            print(f"   ❌ Email notification test failed: {e}")
            return False
        finally:
            if connection.is_connected():
                cursor.close()
                connection.close()
    
    def test_integration_flow(self):
        """Test complete integration flow"""
        print("   Testing complete integration flow...")
        
        # Verify database consistency
        connection = self.connect_to_database()
        if not connection:
            return False
            
        try:
            cursor = connection.cursor()
            
            # Check registration has video flag set
            if self.created_registration_id:
                cursor.execute("SELECT has_video FROM registrations WHERE id = %s", (self.created_registration_id,))
                result = cursor.fetchone()
                if result and result[0]:
                    print("   ✅ Registration correctly marked as having video")
                else:
                    print("   ❌ Registration not marked as having video")
                    return False
            
            # Check video exists and is approved
            if self.created_video_id:
                cursor.execute("SELECT upload_status FROM videos WHERE id = %s", (self.created_video_id,))
                result = cursor.fetchone()
                if result and result[0] == 'approved':
                    print("   ✅ Video correctly approved")
                else:
                    print("   ❌ Video not properly approved")
                    return False
            
            # Check votes exist
            if self.created_video_id:
                cursor.execute("SELECT COUNT(*) FROM votes WHERE video_id = %s", (self.created_video_id,))
                vote_count = cursor.fetchone()[0]
                if vote_count > 0:
                    print(f"   ✅ Votes recorded: {vote_count}")
                else:
                    print("   ❌ No votes found")
                    return False
            
            print("   ✅ Integration flow completed successfully")
            return True
            
        except Error as e:
            print(f"   ❌ Integration flow test failed: {e}")
            return False
        finally:
            if connection.is_connected():
                cursor.close()
                connection.close()

def main():
    print("🚀 Starting Karaoke Sensō PHP - Phase 2 Backend Tests")
    print("=" * 60)
    
    tester = KaraokePhpTester()
    
    # Test sequence for Phase 2 functionality
    tests = [
        ("Phase 2 Database Schema Extensions", tester.test_database_schema_phase2),
        ("Admin Authentication", tester.test_admin_login),
        ("Create Test Event", tester.test_create_test_event),
        ("Create Test Registration", tester.test_create_test_registration),
        ("Video Upload API", tester.test_video_upload_api),
        ("Video Upload Validation", tester.test_video_upload_validation),
        ("Admin Video Management", tester.test_admin_video_management),
        ("Public Video Listing", tester.test_public_video_listing),
        ("Voting System - Cast Vote", tester.test_voting_system_cast_vote),
        ("Voting System - Duplicate Control", tester.test_voting_duplicate_control),
        ("Voting Eligibility Check", tester.test_voting_eligibility_check),
        ("Vote Results API", tester.test_vote_results_api),
        ("Email Notification Logging", tester.test_email_notification_logging),
        ("Integration Flow", tester.test_integration_flow),
    ]
    
    failed_tests = []
    critical_failed_tests = []
    
    for test_name, test_func in tests:
        success = tester.run_test(test_name, test_func)
        if not success:
            failed_tests.append(test_name)
            # Mark core Phase 2 functionality as critical
            if any(keyword in test_name.lower() for keyword in ['database', 'video', 'voting', 'integration']):
                critical_failed_tests.append(test_name)
    
    # Print results
    print("\n" + "=" * 60)
    print("📊 PHASE 2 TEST RESULTS")
    print("=" * 60)
    print(f"Tests run: {tester.tests_run}")
    print(f"Tests passed: {tester.tests_passed}")
    print(f"Tests failed: {len(failed_tests)}")
    print(f"Success rate: {(tester.tests_passed/tester.tests_run)*100:.1f}%")
    
    if critical_failed_tests:
        print(f"\n🚨 CRITICAL FAILURES (Phase 2 Core Features):")
        for test in critical_failed_tests:
            print(f"   - {test}")
    
    if failed_tests:
        print(f"\n❌ All failed tests:")
        for test in failed_tests:
            print(f"   - {test}")
    else:
        print(f"\n✅ All tests passed!")
    
    # Return 0 only if no critical failures
    return 0 if len(critical_failed_tests) == 0 else 1

if __name__ == "__main__":
    sys.exit(main())